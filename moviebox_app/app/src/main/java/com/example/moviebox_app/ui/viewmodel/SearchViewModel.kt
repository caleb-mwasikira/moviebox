package com.example.moviebox_app.ui.viewmodel

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.moviebox_app.data.UiState
import com.example.moviebox_app.data.api.ApiMovie
import com.example.moviebox_app.data.api.ApiRepository
import com.example.moviebox_app.data.api.ApiSeries
import kotlinx.coroutines.Job
import kotlinx.coroutines.delay
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch

data class SearchData(
    val movies: List<ApiMovie>,
    val series: List<ApiSeries>,
)

class SearchViewModel(private val repo: ApiRepository = ApiRepository()) : ViewModel() {

    private val _state = MutableStateFlow<UiState<SearchData>>(UiState.Loading)
    val state: StateFlow<UiState<SearchData>> = _state.asStateFlow()

    private val _query = MutableStateFlow("")
    val query: StateFlow<String> = _query.asStateFlow()

    private val _genre = MutableStateFlow("All")
    val genre: StateFlow<String> = _genre.asStateFlow()

    // Debounce job (delays the search while the user is still typing)
    private var debounceJob: Job? = null
    // Active network job (cancelled if a newer search supersedes it)
    private var activeSearch: Job? = null

    init { search() }

    fun onQueryChange(q: String) {
        _query.value = q
        debounceJob?.cancel()
        debounceJob = viewModelScope.launch {
            delay(300)
            search()
        }
    }

    fun onGenreChange(g: String) {
        _genre.value = g
        debounceJob?.cancel()
        search()
    }

    fun search() {
        // Cancel any in-flight request so its response can't overwrite ours
        activeSearch?.cancel()
        activeSearch = viewModelScope.launch {
            // Keep previous results visible — only show spinner on the very first load
            if (_state.value is UiState.Loading) {
                _state.value = UiState.Loading
            }
            try {
                val result = repo.search(
                    query = _query.value.trim().takeIf { it.isNotBlank() },
                    genre = _genre.value,
                )
                _state.value = UiState.Success(SearchData(result.movies, result.series))
            } catch (e: Exception) {
                if (_state.value !is UiState.Success) {
                    _state.value = UiState.Error(e.message ?: "Search failed")
                }
            }
        }
    }
}
