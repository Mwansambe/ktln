package com.jamexams.app.presentation.subjects

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.jamexams.app.domain.model.Result
import com.jamexams.app.domain.model.Subject
import com.jamexams.app.domain.repository.ExamRepository
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class SubjectViewModel @Inject constructor(
    private val repository: ExamRepository
) : ViewModel() {

    private val _subjects   = MutableStateFlow<List<Subject>>(emptyList())
    private val _isLoading  = MutableStateFlow(false)
    private val _error      = MutableStateFlow<String?>(null)

    val subjects:  StateFlow<List<Subject>> = _subjects.asStateFlow()
    val isLoading: StateFlow<Boolean>       = _isLoading.asStateFlow()
    val error:     StateFlow<String?>       = _error.asStateFlow()

    init { loadSubjects() }

    fun loadSubjects() {
        viewModelScope.launch {
            _isLoading.value = true
            _error.value = null
            when (val result = repository.getSubjects()) {
                is Result.Success -> _subjects.value = result.data
                is Result.Error   -> _error.value = result.message
                else -> {}
            }
            _isLoading.value = false
        }
    }
}
