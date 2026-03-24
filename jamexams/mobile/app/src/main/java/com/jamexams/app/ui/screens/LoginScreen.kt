package com.jamexams.app.ui.screens

import androidx.compose.animation.AnimatedVisibility
import androidx.compose.foundation.*
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.text.KeyboardActions
import androidx.compose.foundation.text.KeyboardOptions
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.focus.FocusDirection
import androidx.compose.ui.graphics.Brush
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.platform.LocalFocusManager
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.*
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.hilt.navigation.compose.hiltViewModel
import com.jamexams.app.presentation.auth.LoginUiState
import com.jamexams.app.presentation.auth.LoginViewModel
import com.jamexams.app.ui.theme.JamGreen
import com.jamexams.app.ui.theme.JamRed

@Composable
fun LoginScreen(
    onLoginSuccess: () -> Unit,
    viewModel: LoginViewModel = hiltViewModel(),
) {
    val uiState          by viewModel.uiState.collectAsState()
    val email            by viewModel.email.collectAsState()
    val password         by viewModel.password.collectAsState()
    val isPasswordVisible by viewModel.isPasswordVisible.collectAsState()
    val isFormValid      by viewModel.isFormValid.collectAsState()
    val focusManager     = LocalFocusManager.current

    LaunchedEffect(uiState) {
        if (uiState is LoginUiState.Success) { onLoginSuccess(); viewModel.resetState() }
    }

    Box(
        Modifier.fillMaxSize().background(Brush.verticalGradient(listOf(JamGreen.copy(alpha = 0.06f), Color.White, JamRed.copy(alpha = 0.04f))))
    ) {
        Column(
            Modifier.fillMaxSize().verticalScroll(rememberScrollState()).padding(horizontal = 28.dp),
            horizontalAlignment = Alignment.CenterHorizontally,
        ) {
            Spacer(Modifier.height(72.dp))
            Card(Modifier.size(96.dp), shape = RoundedCornerShape(24.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(12.dp)) {
                Box(Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Icon(Icons.Filled.MenuBook, "JamExams", tint = JamGreen, modifier = Modifier.size(54.dp))
                }
            }
            Spacer(Modifier.height(18.dp))
            Text("JamExams", style = MaterialTheme.typography.headlineLarge.copy(fontWeight = FontWeight.Bold, letterSpacing = (-0.5).sp), color = Color(0xFF1A1A2E))
            Text("Your Exam Portal", style = MaterialTheme.typography.bodyMedium, color = Color.Gray)
            Spacer(Modifier.height(36.dp))

            Card(Modifier.fillMaxWidth(), shape = RoundedCornerShape(20.dp),
                colors = CardDefaults.cardColors(containerColor = Color.White),
                elevation = CardDefaults.cardElevation(4.dp)) {
                Column(Modifier.padding(24.dp)) {
                    Text("Sign In", style = MaterialTheme.typography.titleLarge.copy(fontWeight = FontWeight.SemiBold))
                    Text("Enter your credentials to continue", style = MaterialTheme.typography.bodySmall, color = Color.Gray)
                    Spacer(Modifier.height(24.dp))

                    OutlinedTextField(value = email, onValueChange = viewModel::onEmailChanged,
                        label = { Text("Email Address") },
                        leadingIcon = { Icon(Icons.Filled.Email, null, tint = JamGreen) },
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Email, imeAction = ImeAction.Next),
                        keyboardActions = KeyboardActions(onNext = { focusManager.moveFocus(FocusDirection.Down) }),
                        singleLine = true, modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp))

                    Spacer(Modifier.height(16.dp))

                    OutlinedTextField(value = password, onValueChange = viewModel::onPasswordChanged,
                        label = { Text("Password") },
                        leadingIcon = { Icon(Icons.Filled.Lock, null, tint = JamGreen) },
                        trailingIcon = {
                            IconButton(onClick = viewModel::togglePasswordVisibility) {
                                Icon(if (isPasswordVisible) Icons.Filled.VisibilityOff else Icons.Filled.Visibility, null)
                            }
                        },
                        visualTransformation = if (isPasswordVisible) VisualTransformation.None else PasswordVisualTransformation(),
                        keyboardOptions = KeyboardOptions(keyboardType = KeyboardType.Password, imeAction = ImeAction.Done),
                        keyboardActions = KeyboardActions(onDone = { focusManager.clearFocus(); if (isFormValid) viewModel.login() }),
                        singleLine = true, modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp))

                    AnimatedVisibility(uiState is LoginUiState.Error || uiState is LoginUiState.AccessExpired || uiState is LoginUiState.NotActivated) {
                        Card(Modifier.fillMaxWidth().padding(top = 12.dp),
                            colors = CardDefaults.cardColors(containerColor = if (uiState is LoginUiState.AccessExpired) Color(0xFFFFF3CD) else Color(0xFFFFEBEE)),
                            shape = RoundedCornerShape(10.dp)) {
                            Row(Modifier.padding(12.dp), verticalAlignment = Alignment.Top) {
                                Icon(Icons.Filled.Warning, null, tint = if (uiState is LoginUiState.AccessExpired) Color(0xFFF59E0B) else JamRed, modifier = Modifier.size(20.dp))
                                Spacer(Modifier.width(8.dp))
                                Text(text = when (val s = uiState) {
                                    is LoginUiState.Error        -> s.message
                                    is LoginUiState.AccessExpired -> s.message
                                    is LoginUiState.NotActivated -> "Your account is pending activation. Contact the administrator."
                                    else -> ""
                                }, style = MaterialTheme.typography.bodySmall)
                            }
                        }
                    }

                    Spacer(Modifier.height(20.dp))
                    Button(
                        onClick = { focusManager.clearFocus(); viewModel.login() },
                        enabled = isFormValid && uiState !is LoginUiState.Loading,
                        modifier = Modifier.fillMaxWidth().height(52.dp),
                        shape = RoundedCornerShape(14.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = JamGreen),
                    ) {
                        if (uiState is LoginUiState.Loading) CircularProgressIndicator(Modifier.size(22.dp), color = Color.White, strokeWidth = 2.5.dp)
                        else Text("Sign In", fontWeight = FontWeight.SemiBold, fontSize = 16.sp)
                    }
                }
            }
            Spacer(Modifier.height(28.dp))
            Text("JamExams © ${java.util.Calendar.getInstance().get(java.util.Calendar.YEAR)}",
                style = MaterialTheme.typography.bodySmall, color = Color.LightGray, textAlign = TextAlign.Center)
            Spacer(Modifier.height(28.dp))
        }
    }
}
