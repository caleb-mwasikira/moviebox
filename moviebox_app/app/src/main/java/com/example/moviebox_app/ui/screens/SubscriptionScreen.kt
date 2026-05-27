package com.example.moviebox_app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.border
import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.rememberScrollState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.foundation.verticalScroll
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.CheckCircle
import androidx.compose.material.icons.filled.Error
import androidx.compose.material.icons.filled.PhoneAndroid
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Icon
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.OutlinedTextFieldDefaults
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.draw.clip
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.moviebox_app.data.api.ApiSubscriptionPackage
import com.example.moviebox_app.ui.theme.Accent
import com.example.moviebox_app.ui.theme.Background
import com.example.moviebox_app.ui.theme.CardBg
import com.example.moviebox_app.ui.theme.DividerColor
import com.example.moviebox_app.ui.theme.MatchGreen
import com.example.moviebox_app.ui.theme.Surface
import com.example.moviebox_app.ui.theme.TagBg
import com.example.moviebox_app.ui.theme.TextMuted
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary
import com.example.moviebox_app.ui.viewmodel.SubscriptionState
import com.example.moviebox_app.ui.viewmodel.SubscriptionViewModel

@Composable
fun SubscriptionScreen(
    onActive: () -> Unit,
    onSkip: () -> Unit,
    vm: SubscriptionViewModel = viewModel(),
) {
    val state by vm.state.collectAsState()

    // Auto-navigate when subscription is confirmed active
    LaunchedEffect(state) {
        if (state is SubscriptionState.Active) onActive()
    }

    Box(
        modifier = Modifier
            .fillMaxSize()
            .background(
                Brush.verticalGradient(listOf(Color(0xFF0D0F1A), Background))
            ),
    ) {
        when (val s = state) {
            is SubscriptionState.Checking -> {
                CircularProgressIndicator(color = Accent, modifier = Modifier.align(Alignment.Center))
            }

            is SubscriptionState.Active -> {
                // Will auto-navigate via LaunchedEffect
                CircularProgressIndicator(color = Accent, modifier = Modifier.align(Alignment.Center))
            }

            is SubscriptionState.LoadError -> {
                Column(
                    modifier = Modifier.fillMaxSize().padding(32.dp),
                    verticalArrangement = Arrangement.Center,
                    horizontalAlignment = Alignment.CenterHorizontally,
                ) {
                    Icon(Icons.Default.Error, null, tint = TextSecondary, modifier = Modifier.size(48.dp))
                    Spacer(Modifier.height(16.dp))
                    Text(s.message, color = TextSecondary, textAlign = TextAlign.Center, fontSize = 14.sp)
                    Spacer(Modifier.height(24.dp))
                    Button(
                        onClick = vm::checkSubscription,
                        colors = ButtonDefaults.buttonColors(containerColor = Accent, contentColor = Color.Black),
                    ) { Text("Retry", fontWeight = FontWeight.Bold) }
                    Spacer(Modifier.height(12.dp))
                    TextButton(onClick = onSkip) {
                        Text("Continue without subscription", color = TextSecondary, fontSize = 13.sp)
                    }
                }
            }

            is SubscriptionState.PackageList -> {
                PackageListContent(
                    state    = s,
                    onSelect = vm::selectPackage,
                    onPhone  = vm::updatePhone,
                    onPay    = vm::initiatePayment,
                    onSkip   = onSkip,
                )
            }

            is SubscriptionState.AwaitingPayment -> {
                AwaitingPaymentContent(pollCount = s.pollCount)
            }

            is SubscriptionState.PaymentSuccess -> {
                PaymentSuccessContent(packageName = s.packageName, onContinue = onActive)
            }

            is SubscriptionState.PaymentFailed -> {
                Column(
                    modifier = Modifier.fillMaxSize().padding(32.dp),
                    verticalArrangement = Arrangement.Center,
                    horizontalAlignment = Alignment.CenterHorizontally,
                ) {
                    Icon(Icons.Default.Error, null, tint = Color(0xFFEF4444), modifier = Modifier.size(56.dp))
                    Spacer(Modifier.height(16.dp))
                    Text("Payment failed", color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 20.sp)
                    Spacer(Modifier.height(8.dp))
                    Text(s.message, color = TextSecondary, textAlign = TextAlign.Center, fontSize = 14.sp)
                    Spacer(Modifier.height(28.dp))
                    Button(
                        onClick = { vm.retryFromFailure(s.packages) },
                        modifier = Modifier.fillMaxWidth(),
                        colors = ButtonDefaults.buttonColors(containerColor = Accent, contentColor = Color.Black),
                    ) { Text("Try Again", fontWeight = FontWeight.Bold) }
                    Spacer(Modifier.height(12.dp))
                    TextButton(onClick = onSkip) {
                        Text("Continue without subscription", color = TextSecondary, fontSize = 13.sp)
                    }
                }
            }
        }
    }
}

@Composable
private fun PackageListContent(
    state: SubscriptionState.PackageList,
    onSelect: (String) -> Unit,
    onPhone: (String) -> Unit,
    onPay: () -> Unit,
    onSkip: () -> Unit,
) {
    Column(
        modifier = Modifier
            .fillMaxSize()
            .verticalScroll(rememberScrollState())
            .padding(horizontal = 24.dp, vertical = 48.dp),
    ) {
        // Header
        Text(
            text = if (state.current != null && !state.current.isActive) "Subscription Expired" else "Choose a Plan",
            color = TextPrimary,
            fontSize = 28.sp,
            fontWeight = FontWeight.ExtraBold,
            textAlign = TextAlign.Center,
            modifier = Modifier.fillMaxWidth(),
        )
        Spacer(Modifier.height(8.dp))
        Text(
            text = if (state.current != null && !state.current.isActive)
                "Your subscription has expired. Renew to continue watching."
            else
                "Unlock unlimited movies, series, and more.",
            color = TextSecondary,
            fontSize = 14.sp,
            textAlign = TextAlign.Center,
            lineHeight = 20.sp,
            modifier = Modifier.fillMaxWidth(),
        )

        Spacer(Modifier.height(32.dp))

        // Package cards
        state.packages.forEach { pkg ->
            PackageCard(
                pkg      = pkg,
                selected = pkg.uuid == state.selectedUuid,
                onClick  = { onSelect(pkg.uuid) },
            )
            Spacer(Modifier.height(12.dp))
        }

        Spacer(Modifier.height(24.dp))

        // Phone input
        OutlinedTextField(
            value = state.phone,
            onValueChange = onPhone,
            label = { Text("M-Pesa Phone Number", color = TextMuted) },
            placeholder = { Text("e.g. 0712 345 678", color = TextMuted) },
            leadingIcon = { Icon(Icons.Default.PhoneAndroid, null, tint = TextSecondary) },
            keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Phone),
            singleLine = true,
            isError = state.error != null,
            supportingText = state.error?.let { { Text(it, color = Color(0xFFEF4444), fontSize = 12.sp) } },
            colors = OutlinedTextFieldDefaults.colors(
                focusedTextColor      = TextPrimary,
                unfocusedTextColor    = TextPrimary,
                focusedBorderColor    = Accent,
                unfocusedBorderColor  = DividerColor,
                cursorColor           = Accent,
            ),
            modifier = Modifier.fillMaxWidth(),
        )

        Spacer(Modifier.height(20.dp))

        Button(
            onClick = onPay,
            enabled = !state.isSubmitting,
            modifier = Modifier.fillMaxWidth().height(52.dp),
            shape = RoundedCornerShape(12.dp),
            colors = ButtonDefaults.buttonColors(containerColor = Accent, contentColor = Color.Black),
        ) {
            if (state.isSubmitting) {
                CircularProgressIndicator(color = Color.Black, modifier = Modifier.size(20.dp), strokeWidth = 2.dp)
            } else {
                Text("Subscribe via M-Pesa", fontWeight = FontWeight.ExtraBold, fontSize = 16.sp)
            }
        }

        Spacer(Modifier.height(16.dp))

        TextButton(
            onClick = onSkip,
            modifier = Modifier.fillMaxWidth(),
        ) {
            Text("Continue without subscription", color = TextMuted, fontSize = 13.sp)
        }
    }
}

@Composable
private fun PackageCard(
    pkg: ApiSubscriptionPackage,
    selected: Boolean,
    onClick: () -> Unit,
) {
    Box(
        modifier = Modifier
            .fillMaxWidth()
            .clip(RoundedCornerShape(14.dp))
            .background(if (selected) Accent.copy(alpha = 0.1f) else CardBg)
            .border(
                width = if (selected) 2.dp else 1.dp,
                color = if (selected) Accent else DividerColor,
                shape = RoundedCornerShape(14.dp),
            )
            .clickable(onClick = onClick)
            .padding(16.dp),
    ) {
        Row(verticalAlignment = Alignment.CenterVertically) {
            Column(modifier = Modifier.weight(1f)) {
                Row(verticalAlignment = Alignment.CenterVertically, horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    Text(pkg.name, color = if (selected) Accent else TextPrimary, fontWeight = FontWeight.Bold, fontSize = 16.sp)
                    if (pkg.interval == "yearly") {
                        Box(
                            modifier = Modifier
                                .clip(RoundedCornerShape(4.dp))
                                .background(MatchGreen.copy(alpha = 0.2f))
                                .padding(horizontal = 6.dp, vertical = 2.dp),
                        ) { Text("BEST VALUE", color = MatchGreen, fontSize = 9.sp, fontWeight = FontWeight.Bold) }
                    }
                }
                Spacer(Modifier.height(4.dp))
                Text(
                    text = "${pkg.formattedPrice()} / ${if (pkg.interval == "yearly") "year" else "month"}",
                    color = TextSecondary,
                    fontSize = 13.sp,
                )
                if (!pkg.description.isNullOrBlank()) {
                    Spacer(Modifier.height(6.dp))
                    Text(pkg.description, color = TextMuted, fontSize = 12.sp, lineHeight = 18.sp)
                }
                Spacer(Modifier.height(8.dp))
                Row(horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                    pkg.maxResolution?.let { res ->
                        FeaturePill(res.uppercase())
                    }
                    FeaturePill("${pkg.maxScreens} screen${if (pkg.maxScreens > 1) "s" else ""}")
                    if (!pkg.hasAds) FeaturePill("No ads")
                    if (pkg.downloadEnabled) FeaturePill("Downloads")
                }
            }
            if (selected) {
                Spacer(Modifier.width(12.dp))
                Icon(Icons.Default.CheckCircle, null, tint = Accent, modifier = Modifier.size(24.dp))
            }
        }
    }
}

@Composable
private fun FeaturePill(label: String) {
    Box(
        modifier = Modifier
            .clip(RoundedCornerShape(4.dp))
            .background(TagBg)
            .padding(horizontal = 6.dp, vertical = 3.dp),
    ) {
        Text(label, color = TextSecondary, fontSize = 10.sp)
    }
}

@Composable
private fun AwaitingPaymentContent(pollCount: Int) {
    Column(
        modifier = Modifier.fillMaxSize().padding(32.dp),
        verticalArrangement = Arrangement.Center,
        horizontalAlignment = Alignment.CenterHorizontally,
    ) {
        CircularProgressIndicator(color = Accent, modifier = Modifier.size(56.dp), strokeWidth = 3.dp)
        Spacer(Modifier.height(24.dp))
        Text("Waiting for payment", color = TextPrimary, fontWeight = FontWeight.Bold, fontSize = 20.sp)
        Spacer(Modifier.height(8.dp))
        Text(
            text = "Check your phone and enter your M-Pesa PIN to complete the payment.",
            color = TextSecondary,
            fontSize = 14.sp,
            textAlign = TextAlign.Center,
            lineHeight = 22.sp,
        )
        if (pollCount > 2) {
            Spacer(Modifier.height(16.dp))
            Text("Checking status…", color = TextMuted, fontSize = 12.sp)
        }
    }
}

@Composable
private fun PaymentSuccessContent(packageName: String, onContinue: () -> Unit) {
    Column(
        modifier = Modifier.fillMaxSize().padding(32.dp),
        verticalArrangement = Arrangement.Center,
        horizontalAlignment = Alignment.CenterHorizontally,
    ) {
        Icon(Icons.Default.CheckCircle, null, tint = MatchGreen, modifier = Modifier.size(72.dp))
        Spacer(Modifier.height(20.dp))
        Text("You're subscribed!", color = TextPrimary, fontWeight = FontWeight.ExtraBold, fontSize = 24.sp)
        Spacer(Modifier.height(8.dp))
        Text(
            text = "Welcome to $packageName. Enjoy unlimited streaming.",
            color = TextSecondary,
            fontSize = 14.sp,
            textAlign = TextAlign.Center,
        )
        Spacer(Modifier.height(32.dp))
        Button(
            onClick = onContinue,
            modifier = Modifier.fillMaxWidth().height(52.dp),
            shape = RoundedCornerShape(12.dp),
            colors = ButtonDefaults.buttonColors(containerColor = Accent, contentColor = Color.Black),
        ) {
            Text("Start Watching", fontWeight = FontWeight.ExtraBold, fontSize = 16.sp)
        }
    }
}
