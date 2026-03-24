package com.jamexams.app.data.api

import com.jamexams.app.data.model.*
import retrofit2.Response
import retrofit2.http.*

/**
 * ApiService - Retrofit interface for all JamExams API endpoints.
 * Every response follows the standard format: { status, message, data }
 */
interface ApiService {

    // ==================== AUTHENTICATION ====================

    @POST("auth/login")
    suspend fun login(@Body request: LoginRequest): Response<ApiResponse<AuthData>>

    @POST("auth/logout")
    suspend fun logout(): Response<ApiResponse<Unit>>

    @GET("auth/me")
    suspend fun getProfile(): Response<ApiResponse<UserData>>

    @PUT("auth/fcm-token")
    suspend fun updateFcmToken(@Body request: FcmTokenRequest): Response<ApiResponse<Unit>>

    // ==================== SUBJECTS ====================

    @GET("subjects")
    suspend fun getSubjects(): Response<ApiResponse<SubjectsData>>

    // ==================== EXAMS ====================

    @GET("exams")
    suspend fun getExams(
        @Query("subject_id")  subjectId:  Int?    = null,
        @Query("class_level") classLevel: String? = null,
        @Query("exam_type")   examType:   String? = null,
        @Query("year")        year:       Int?    = null,
        @Query("search")      search:     String? = null,
        @Query("featured")    featured:   Boolean? = null,
        @Query("page")        page:       Int     = 1,
        @Query("per_page")    perPage:    Int     = 20,
    ): Response<ApiResponse<ExamsData>>

    @GET("exams/{id}")
    suspend fun getExam(@Path("id") id: Int): Response<ApiResponse<ExamDetailData>>

    @GET("exams/{id}/download")
    @Streaming
    suspend fun downloadExam(@Path("id") id: Int): Response<okhttp3.ResponseBody>

    @GET("exams/{id}/marking-scheme")
    @Streaming
    suspend fun downloadMarkingScheme(@Path("id") id: Int): Response<okhttp3.ResponseBody>
}
