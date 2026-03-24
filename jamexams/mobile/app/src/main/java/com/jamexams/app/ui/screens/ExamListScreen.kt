package com.jamexams.app.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.lazy.rememberLazyListState
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import com.jamexams.app.domain.model.Exam
import com.jamexams.app.presentation.exams.ExamListUiState
import com.jamexams.app.presentation.exams.ExamListViewModel
import com.jamexams.app.ui.theme.JamGreen
import com.jamexams.app.ui.theme.JamRed

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ExamListScreen(
    onExamClick: (Int) -> Unit,
    viewModel: ExamListViewModel = hiltViewModel(),
) {
    val uiState by viewModel.uiState.collectAsState()
    val searchQuery by viewModel.searchQuery.collectAsState()
    val listState = rememberLazyListState()

    val shouldLoadMore by remember {
        derivedStateOf {
            val lastVisible = listState.layoutInfo.visibleItemsInfo.lastOrNull()?.index ?: 0
            lastVisible >= listState.layoutInfo.totalItemsCount - 3
        }
    }
    LaunchedEffect(shouldLoadMore) { if (shouldLoadMore) viewModel.loadMoreExams() }

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Exams", fontWeight = FontWeight.Bold) },
                actions = { IconButton(onClick = viewModel::refresh) { Icon(Icons.Filled.Refresh, "Refresh") } },
                colors = TopAppBarDefaults.topAppBarColors(containerColor = Color.White),
            )
        }
    ) { padding ->
        Column(Modifier.fillMaxSize().padding(padding).padding(horizontal = 16.dp)) {
            Spacer(Modifier.height(8.dp))
            OutlinedTextField(
                value = searchQuery,
                onValueChange = viewModel::onSearchQueryChanged,
                placeholder = { Text("Search exams...") },
                leadingIcon = { Icon(Icons.Filled.Search, null, tint = JamGreen) },
                trailingIcon = { if (searchQuery.isNotEmpty()) IconButton(onClick = { viewModel.onSearchQueryChanged("") }) { Icon(Icons.Filled.Clear, null) } },
                singleLine = true,
                modifier = Modifier.fillMaxWidth(),
                shape = RoundedCornerShape(14.dp),
            )
            Spacer(Modifier.height(12.dp))

            when (val state = uiState) {
                is ExamListUiState.Loading -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        CircularProgressIndicator(color = JamGreen)
                        Spacer(Modifier.height(12.dp))
                        Text("Loading exams...", color = Color.Gray)
                    }
                }
                is ExamListUiState.Empty -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Icon(Icons.Filled.SearchOff, null, tint = Color.LightGray, modifier = Modifier.size(64.dp))
                        Spacer(Modifier.height(12.dp))
                        Text("No exams found", fontWeight = FontWeight.SemiBold, color = Color.Gray)
                    }
                }
                is ExamListUiState.Error -> Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Icon(Icons.Filled.WifiOff, null, tint = JamRed.copy(alpha = 0.6f), modifier = Modifier.size(64.dp))
                        Spacer(Modifier.height(12.dp))
                        Text("Failed to load exams", fontWeight = FontWeight.SemiBold)
                        Text(state.message, style = MaterialTheme.typography.bodySmall, color = Color.Gray)
                        Spacer(Modifier.height(16.dp))
                        Button(onClick = viewModel::retry, colors = ButtonDefaults.buttonColors(containerColor = JamGreen)) {
                            Icon(Icons.Filled.Refresh, null); Spacer(Modifier.width(6.dp)); Text("Retry")
                        }
                    }
                }
                is ExamListUiState.Success, is ExamListUiState.LoadingMore -> {
                    val exams = when (state) {
                        is ExamListUiState.Success -> state.exams
                        is ExamListUiState.LoadingMore -> state.exams
                        else -> emptyList()
                    }
                    LazyColumn(state = listState, verticalArrangement = Arrangement.spacedBy(10.dp)) {
                        items(exams, key = { it.id }) { exam -> ExamCard(exam = exam, onClick = { onExamClick(exam.id) }) }
                        if (state is ExamListUiState.LoadingMore) {
                            item { Box(Modifier.fillMaxWidth().padding(16.dp), contentAlignment = Alignment.Center) { CircularProgressIndicator(Modifier.size(28.dp), color = JamGreen) } }
                        }
                        item { Spacer(Modifier.height(16.dp)) }
                    }
                }
            }
        }
    }
}

@Composable
fun ExamCard(exam: Exam, onClick: () -> Unit) {
    Card(onClick = onClick, modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(14.dp),
        colors = CardDefaults.cardColors(containerColor = Color.White), elevation = CardDefaults.cardElevation(2.dp)) {
        Row(Modifier.padding(16.dp), verticalAlignment = Alignment.CenterVertically) {
            val subjectColor = try { Color(android.graphics.Color.parseColor(exam.subject?.color ?: "#1B8B4B")) } catch (e: Exception) { JamGreen }
            Card(Modifier.size(46.dp), shape = RoundedCornerShape(12.dp), colors = CardDefaults.cardColors(containerColor = subjectColor.copy(alpha = 0.15f))) {
                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) { Icon(Icons.Filled.Description, null, tint = subjectColor, modifier = Modifier.size(24.dp)) }
            }
            Spacer(Modifier.width(12.dp))
            Column(Modifier.weight(1f)) {
                Text(exam.title, fontWeight = FontWeight.SemiBold, maxLines = 2)
                Spacer(Modifier.height(4.dp))
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Text(exam.subject?.name ?: "Unknown", style = MaterialTheme.typography.labelSmall, color = subjectColor)
                    exam.classLevel?.let { Text(" • $it", style = MaterialTheme.typography.labelSmall, color = Color.Gray) }
                    exam.year?.let { Text(" • $it", style = MaterialTheme.typography.labelSmall, color = Color.Gray) }
                }
                Spacer(Modifier.height(4.dp))
                Row {
                    AssistChip(onClick = {}, label = { Text(exam.examType, style = MaterialTheme.typography.labelSmall) }, modifier = Modifier.height(22.dp))
                    if (exam.hasMarkingScheme) {
                        Spacer(Modifier.width(6.dp))
                        AssistChip(onClick = {}, label = { Text("✓ Marking", style = MaterialTheme.typography.labelSmall) }, modifier = Modifier.height(22.dp))
                    }
                }
            }
            Icon(Icons.Filled.ChevronRight, null, tint = Color.LightGray)
        }
    }
}
