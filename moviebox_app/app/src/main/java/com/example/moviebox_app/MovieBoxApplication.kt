package com.example.moviebox_app

import android.app.Application
import com.example.moviebox_app.data.auth.SessionManager

class MovieBoxApplication : Application() {

    override fun onCreate() {
        super.onCreate()
        sessionManager = SessionManager(this)
    }

    companion object {
        lateinit var sessionManager: SessionManager
    }
}
