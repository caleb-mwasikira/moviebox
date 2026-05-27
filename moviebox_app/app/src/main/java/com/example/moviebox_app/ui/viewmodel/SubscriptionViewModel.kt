package com.example.moviebox_app.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.moviebox_app.data.api.ApiCurrentSubscription
import com.example.moviebox_app.data.api.ApiRepository
import com.example.moviebox_app.data.api.ApiSubscriptionPackage
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch

sealed class SubscriptionState {
    data object Checking : SubscriptionState()
    data object Active : SubscriptionState()
    data class PackageList(
        val packages: List<ApiSubscriptionPackage>,
        val current: ApiCurrentSubscription?,
        val selectedUuid: String = packages.firstOrNull()?.uuid ?: "",
        val phone: String = "",
        val error: String? = null,
        val isSubmitting: Boolean = false,
    ) : SubscriptionState()
    data class AwaitingPayment(val checkoutRequestId: String, val pollCount: Int = 0) : SubscriptionState()
    data class PaymentSuccess(val packageName: String) : SubscriptionState()
    data class PaymentFailed(val message: String, val packages: List<ApiSubscriptionPackage>) : SubscriptionState()
    data class LoadError(val message: String) : SubscriptionState()
}

class SubscriptionViewModel : ViewModel() {

    private val repo = ApiRepository()

    private val _state = MutableStateFlow<SubscriptionState>(SubscriptionState.Checking)
    val state: StateFlow<SubscriptionState> = _state

    private var pollJob: Job? = null

    init { checkSubscription() }

    fun checkSubscription() {
        viewModelScope.launch {
            _state.value = SubscriptionState.Checking
            try {
                val current = repo.getCurrentSubscription()
                if (current != null && current.isActive) {
                    _state.value = SubscriptionState.Active
                } else {
                    val packages = repo.getSubscriptionPackages()
                    _state.value = SubscriptionState.PackageList(
                        packages = packages,
                        current  = current,
                        selectedUuid = packages.firstOrNull()?.uuid ?: "",
                    )
                }
            } catch (e: Exception) {
                _state.value = SubscriptionState.LoadError(e.message ?: "Failed to load subscription info")
            }
        }
    }

    fun selectPackage(uuid: String) {
        val s = _state.value as? SubscriptionState.PackageList ?: return
        _state.value = s.copy(selectedUuid = uuid, error = null)
    }

    fun updatePhone(phone: String) {
        val s = _state.value as? SubscriptionState.PackageList ?: return
        _state.value = s.copy(phone = phone, error = null)
    }

    fun initiatePayment() {
        val s = _state.value as? SubscriptionState.PackageList ?: return
        if (s.selectedUuid.isEmpty()) {
            _state.value = s.copy(error = "Please select a package")
            return
        }
        if (s.phone.isBlank()) {
            _state.value = s.copy(error = "Enter your M-Pesa phone number")
            return
        }
        _state.value = s.copy(isSubmitting = true, error = null)

        viewModelScope.launch {
            try {
                val result = repo.initiateSubscription(s.phone, s.selectedUuid)
                if (result.success && result.checkoutRequestId != null) {
                    _state.value = SubscriptionState.AwaitingPayment(result.checkoutRequestId)
                    startPolling(result.checkoutRequestId, s.packages)
                } else {
                    _state.value = s.copy(isSubmitting = false, error = result.message)
                }
            } catch (e: Exception) {
                _state.value = s.copy(
                    isSubmitting = false,
                    error = e.message ?: "Payment request failed. Try again.",
                )
            }
        }
    }

    private fun startPolling(checkoutRequestId: String, packages: List<ApiSubscriptionPackage>) {
        pollJob?.cancel()
        pollJob = viewModelScope.launch {
            repeat(24) { attempt -> // poll for up to 2 minutes (24 × 5s)
                delay(5_000)
                try {
                    val status = repo.getSubscriptionStatus(checkoutRequestId)
                    when (status.status) {
                        "completed" -> {
                            val pkgName = packages.firstOrNull { pkg ->
                                (_state.value as? SubscriptionState.AwaitingPayment) != null
                            }?.name ?: "your plan"
                            _state.value = SubscriptionState.PaymentSuccess(pkgName)
                            return@launch
                        }
                        "failed", "cancelled" -> {
                            _state.value = SubscriptionState.PaymentFailed(
                                message  = status.message ?: "Payment was not completed.",
                                packages = packages,
                            )
                            return@launch
                        }
                        else -> {
                            _state.value = SubscriptionState.AwaitingPayment(checkoutRequestId, attempt + 1)
                        }
                    }
                } catch (_: Exception) { }
            }
            // Timed out
            _state.value = SubscriptionState.PaymentFailed(
                message  = "Payment timed out. If you completed the M-Pesa prompt, please wait and try again.",
                packages = packages,
            )
        }
    }

    fun retryFromFailure(packages: List<ApiSubscriptionPackage>) {
        pollJob?.cancel()
        _state.value = SubscriptionState.PackageList(packages = packages, current = null)
    }

    override fun onCleared() {
        pollJob?.cancel()
    }
}
