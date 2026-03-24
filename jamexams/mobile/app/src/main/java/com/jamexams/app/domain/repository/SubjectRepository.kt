package com.jamexams.app.domain.repository

import com.jamexams.app.domain.model.Subject
import com.jamexams.app.domain.model.Result
import kotlinx.coroutines.flow.Flow

interface SubjectRepository {
    suspend fun getSubjects(): Result<List<Subject>>
    fun getCachedSubjects(): Flow<List<Subject>>
}
