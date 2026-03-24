package com.jamexams.app.di

import android.content.Context
import com.jamexams.app.data.api.ApiClient
import com.jamexams.app.data.api.ApiService
import com.jamexams.app.data.local.TokenDataStore
import com.jamexams.app.data.repository.AuthRepositoryImpl
import com.jamexams.app.data.repository.ExamRepositoryImpl
import com.jamexams.app.domain.repository.AuthRepository
import com.jamexams.app.domain.repository.ExamRepository
import dagger.Module
import dagger.Provides
import dagger.hilt.InstallIn
import dagger.hilt.android.qualifiers.ApplicationContext
import dagger.hilt.components.SingletonComponent
import javax.inject.Singleton

/**
 * AppModule - Hilt dependency injection module.
 * Wires all services, repositories, and utilities together.
 */
@Module
@InstallIn(SingletonComponent::class)
object AppModule {

    @Provides @Singleton
    fun provideTokenDataStore(@ApplicationContext context: Context): TokenDataStore =
        TokenDataStore(context)

    @Provides @Singleton
    fun provideApiService(tokenDataStore: TokenDataStore): ApiService =
        ApiClient.create(tokenDataStore)

    @Provides @Singleton
    fun provideAuthRepository(
        api: ApiService,
        tokenDataStore: TokenDataStore
    ): AuthRepository = AuthRepositoryImpl(api, tokenDataStore)

    @Provides @Singleton
    fun provideExamRepository(
        api: ApiService,
        @ApplicationContext context: Context
    ): ExamRepository = ExamRepositoryImpl(api, context)
}
