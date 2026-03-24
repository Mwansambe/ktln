package com.jamexams.app

import android.app.Application
import dagger.hilt.android.HiltAndroidApp

/**
 * JamExamsApp - Application entry point.
 * Annotated with @HiltAndroidApp to trigger Hilt dependency injection.
 */
@HiltAndroidApp
class JamExamsApp : Application()
