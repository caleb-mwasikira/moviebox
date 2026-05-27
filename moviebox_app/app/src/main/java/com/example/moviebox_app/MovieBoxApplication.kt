package com.example.moviebox_app

import android.app.Application
import androidx.room.Room
import com.example.moviebox_app.data.LocalRepository
import com.example.moviebox_app.data.WatchProgressStore
import com.example.moviebox_app.data.auth.SessionManager
import com.example.moviebox_app.data.db.AppDatabase

class MovieBoxApplication : Application() {

    override fun onCreate() {
        super.onCreate()
        sessionManager = SessionManager(this)
        progressStore = WatchProgressStore(this)
        db = Room.databaseBuilder(this, AppDatabase::class.java, "moviebox.db")
            .fallbackToDestructiveMigration(true)
            .build()
        localRepo = LocalRepository(db)
    }

    companion object {
        lateinit var sessionManager: SessionManager
        lateinit var progressStore: WatchProgressStore
        lateinit var db: AppDatabase
        lateinit var localRepo: LocalRepository
    }
}
