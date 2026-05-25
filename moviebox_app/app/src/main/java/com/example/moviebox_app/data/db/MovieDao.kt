package com.example.moviebox_app.data.db

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query

@Dao
interface MovieDao {
    @Query("SELECT * FROM movies ORDER BY featured DESC, createdAt DESC")
    suspend fun getAll(): List<MovieEntity>

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    suspend fun upsertAll(movies: List<MovieEntity>)

    @Query("DELETE FROM movies")
    suspend fun deleteAll()
}
