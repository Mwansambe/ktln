package com.jamexams.app.domain.repository

import com.jamexams.app.domain.model.Exam
import com.jamexams.app.domain.model.ExamFilter
import com.jamexams.app.domain.model.Result
import com.jamexams.app.domain.model.Subject

interface ExamRepository {
    suspend fun getSubjects(): Result<List<Subject>>
    suspend fun getExams(filter: ExamFilter, page: Int): Result<Pair<List<Exam>, Int>>
    suspend fun getExam(id: Int): Result<Exam>
    suspend fun downloadExam(id: Int, fileName: String): Result<String>
    suspend fun downloadMarkingScheme(id: Int, fileName: String): Result<String>
}
