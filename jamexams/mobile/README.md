# JamExams Mobile App

Android application built with Kotlin + Jetpack Compose.

## Setup
1. Open `mobile/` in Android Studio
2. Add `google-services.json` from Firebase Console to `app/`
3. Update `BASE_URL` in `app/build.gradle.kts`
4. Sync Gradle and Run

## Architecture
- **MVVM** + Clean Architecture
- **Hilt** for dependency injection
- **StateFlow** for reactive UI
- **Retrofit** for networking
- **DataStore** for token storage
- **FCM** for push notifications

## Package: `com.jamexams.app`
