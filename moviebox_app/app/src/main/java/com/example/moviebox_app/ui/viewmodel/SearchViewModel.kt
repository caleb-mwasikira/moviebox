package com.example.moviebox_app.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.moviebox_app.data.UiState
import com.example.moviebox_app.data.api.ApiMovie
import com.example.moviebox_app.data.api.ApiRepository
import kotlinx.coroutines.CoroutineExceptionHandler
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

class SearchViewModel(private val repo: ApiRepository = ApiRepository()) : ViewModel() {

    private val _state = MutableStateFlow<UiState<List<ApiMovie>>>(UiState.Loading)
    val state: StateFlow<UiState<List<ApiMovie>>> = _state.asStateFlow()

    private val _query = MutableStateFlow("")
    val query: StateFlow<String> = _query.asStateFlow()

    private val _genre = MutableStateFlow("All")
    val genre: StateFlow<String> = _genre.asStateFlow()

    private val exceptionHandler = CoroutineExceptionHandler { _, throwable ->
        _state.value = UiState.Error(throwable.message ?: "Search failed")
    }

    private var searchJob: Job? = null

    init { search() }

    fun onQueryChange(q: String) {
        _query.value = q
        searchJob?.cancel()
        searchJob = viewModelScope.launch {
            delay(300)
            search()
        }
    }

    fun onGenreChange(g: String) {
        _genre.value = g
        search()
    }

    fun search() {
        viewModelScope.launch(exceptionHandler) {
            _state.value = UiState.Loading
            val results = repo.getMovies(
                query = _query.value.takeIf { it.isNotBlank() },
                genre = _genre.value,
            )
            _state.value = UiState.Success(results)
        }
    }
}
