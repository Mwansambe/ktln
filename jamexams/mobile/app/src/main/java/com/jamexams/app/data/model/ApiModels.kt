package com.jamexams.app.data.model

import com.google.gson.annotations.SerializedName

// ==================== STANDARD API WRAPPER ====================

data class ApiResponse<T>(
    @SerializedName("status")  val status:  String,
    @SerializedName("message") val message: String,
    @SerializedName("data")    val data:    T? = null,
    @SerializedName("code")    val code:    String? = null,
)

// ==================== AUTH MODELS ====================

data class LoginRequest(
    @SerializedName("email")     val email:    String,
    @SerializedName("password")  val password: String,
    @SerializedName("fcm_token") val fcmToken: String? = null,
)

data class FcmTokenRequest(
    @SerializedName("fcm_token") val fcmToken: String,
)

data class AuthData(
    @SerializedName("token") val token: String,
    @SerializedName("user")  val user:  UserData,
)

data class UserData(
    @SerializedName("id")             val id:           String,
    @SerializedName("name")           val name:         String,
    @SerializedName("email")          val email:        String,
    @SerializedName("phone")          val phone:        String?,
    @SerializedName("avatar")         val avatar:       String?,
    @SerializedName("is_active")      val isActive:     Boolean,
    @SerializedName("activated_at")   val activatedAt:  String?,
    @SerializedName("expires_at")     val expiresAt:    String?,
    @SerializedName("days_remaining") val daysRemaining: Int,
    @SerializedName("roles")          val roles:        List<String>,
)

// ==================== SUBJECT MODELS ====================

data class SubjectsData(
    @SerializedName("subjects") val subjects: List<SubjectData>,
)

data class SubjectData(
    @SerializedName("id")          val id:          Int,
    @SerializedName("name")        val name:        String,
    @SerializedName("code")        val code:        String,
    @SerializedName("description") val description: String?,
    @SerializedName("icon")        val icon:        String?,
    @SerializedName("color")       val color:       String?,
    @SerializedName("exam_count")  val examCount:   Int,
)

// ==================== EXAM MODELS ====================

data class ExamsData(
    @SerializedName("exams")      val exams:      List<ExamData>,
    @SerializedName("pagination") val pagination: PaginationData,
)

data class ExamDetailData(
    @SerializedName("exam") val exam: ExamData,
)

data class ExamData(
    @SerializedName("id")                  val id:                 Int,
    @SerializedName("code")                val code:               String,
    @SerializedName("title")               val title:              String,
    @SerializedName("description")         val description:        String?,
    @SerializedName("exam_type")           val examType:           String,
    @SerializedName("class_level")         val classLevel:         String?,
    @SerializedName("year")                val year:               Int?,
    @SerializedName("has_marking_scheme")  val hasMarkingScheme:   Boolean,
    @SerializedName("exam_file_size")      val examFileSize:       Long,
    @SerializedName("marking_scheme_size") val markingSchemeSize:  Long,
    @SerializedName("is_featured")         val isFeatured:         Boolean,
    @SerializedName("download_count")      val downloadCount:      Int,
    @SerializedName("subject")             val subject:            SubjectData?,
    @SerializedName("created_at")          val createdAt:          String,
)

data class PaginationData(
    @SerializedName("current_page") val currentPage: Int,
    @SerializedName("last_page")    val lastPage:    Int,
    @SerializedName("per_page")     val perPage:     Int,
    @SerializedName("total")        val total:       Int,
)
