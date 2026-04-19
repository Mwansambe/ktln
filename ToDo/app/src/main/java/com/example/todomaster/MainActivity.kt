package com.example.todomaster

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Surface
import androidx.compose.runtime.Composable
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.ui.Modifier
import com.example.todomaster.ui.theme.ToDoMasterTheme

data class Task(
    val id: Int,
    val title: String,
    var isCompleted: Boolean = false
)
class MainActivity : ComponentActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContent {
            ToDoMasterTheme {
                Surface(
                    modifier = Modifier.fillMaxSize(),
                    color = MaterialTheme.colorScheme.background
                ) {
                    Counter()
                }
            }
        }
    }
}


@Composable
fun Counter(){
    val count = remember { mutableStateOf(0) }
    val tasks = remember { mutableStateOf(listOf<Task>()) }
    Text(
        text = "we'com"
    )
}