package com.example.moviebox_app.data.db

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query

@Dao
interface SeriesDao {
    @Query("SELECT * FROM series ORDER BY featured DESC, createdAt DESC")
    suspend fun getAll(): List<SeriesEntity>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun upsertAll(series: List<SeriesEntity>)

    @Query("DELETE FROM series")
    suspend fun deleteAll()
}
