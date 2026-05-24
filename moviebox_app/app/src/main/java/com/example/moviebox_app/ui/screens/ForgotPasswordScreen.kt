package com.example.moviebox_app.ui.screens

import androidx.compose.foundation.background
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.automirrored.filled.ArrowBack
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.setValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.KeyboardType
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.input.VisualTransformation
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.lifecycle.viewmodel.compose.viewModel
import com.example.moviebox_app.ui.theme.Accent
import com.example.moviebox_app.ui.theme.Background
import com.example.moviebox_app.ui.theme.TextMuted
import com.example.moviebox_app.ui.theme.TextPrimary
import com.example.moviebox_app.ui.theme.TextSecondary
import com.example.moviebox_app.ui.viewmodel.AuthUiState
import com.example.moviebox_app.ui.viewmodel.AuthViewModel

@Composable
fun ForgotPasswordScreen(
    onResetSuccess: () -> Unit,
    onBack: () -> Unit,
    vm: AuthViewModel = viewModel(),
) {
    val state by vm.state.collectAsState()

    var email               by remember { mutableStateOf("") }
    var otp                 by remember { mutableStateOf("") }
    var password            by remember { mutableStateOf("") }
    var passwordConfirm     by remember { mutableStateOf("") }
    var step                by remember { mutableStateOf(1) }

    LaunchedEffect(state) {
        if (state is AuthUiState.Success) onResetSuccess()
    }

    Box(modifier = Modifier.fillMaxSize().background(Background)) {
        Column(
            modifier = Modifier
                .fillMaxSize()
                .padding(horizontal = 28.dp, vertical = 48.dp),
            verticalArrangement = Arrangement.Center,
        ) {
            IconButton(onClick = {
                if (step == 2) { step = 1; vm.resetState() } else onBack()
            }) {
                Icon(Icons.AutoMirrored.Filled.ArrowBack, null, tint = TextPrimary)
            }

            Spacer(Modifier.height(16.dp))

            if (step == 1) {
                Text("Forgot password?", color = TextMuted, fontSize = 14.sp)
                Spacer(Modifier.height(4.dp))
                Text("Reset Password", color = TextPrimary, fontWeight = FontWeight.ExtraBold, fontSize = 28.sp)

                Spacer(Modifier.height(12.dp))

                Text(
                    "Enter your email address and we'll send a 6-digit code.",
                    color = TextSecondary,
                    fontSize = 14.sp,
                    lineHeight = 20.sp,
                )

                Spacer(Modifier.height(32.dp))

                AuthField(
                    value = email,
                    onValueChange = { email = it },
                    placeholder = "Email address",
                    keyboardType = KeyboardType.Email,
                )

                if (state is AuthUiState.Error) {
                    Spacer(Modifier.height(12.dp))
                    Text((state as AuthUiState.Error).message, color = Color(0xFFEF4444), fontSize = 13.sp)
                }

                Spacer(Modifier.height(28.dp))

                Button(
                    onClick = { vm.forgotPassword(email) { step = 2 } },
                    enabled = state !is AuthUiState.Loading,
                    modifier = Modifier.fillMaxWidth().height(52.dp),
                    shape = RoundedCornerShape(12.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = Accent, contentColor = Color.Black),
                ) {
                    if (state is AuthUiState.Loading) {
                        CircularProgressIndicator(color = Color.Black, modifier = Modifier.size(20.dp), strokeWidth = 2.dp)
                    } else {
                        Text("Send Code", fontWeight = FontWeight.Bold, fontSize = 15.sp)
                    }
                }
            } else {
                Text("Check your email", color = TextMuted, fontSize = 14.sp)
                Spacer(Modifier.height(4.dp))
                Text("Enter Reset Code", color = TextPrimary, fontWeight = FontWeight.ExtraBold, fontSize = 28.sp)

                Spacer(Modifier.height(12.dp))

                Text(
                    "We sent a 6-digit code to $email. Enter it below along with your new password.",
                    color = TextSecondary,
                    fontSize = 14.sp,
                    lineHeight = 20.sp,
                )

                Spacer(Modifier.height(32.dp))

                AuthField(
                    value = otp,
                    onValueChange = { if (it.length <= 6) otp = it },
                    placeholder = "6-digit code",
                    keyboardType = KeyboardType.NumberPassword,
                )

                Spacer(Modifier.height(14.dp))

                AuthField(
                    value = password,
                    onValueChange = { password = it },
                    placeholder = "New password",
                    keyboardType = KeyboardType.Password,
                    visualTransformation = PasswordVisualTransformation(),
                )

                Spacer(Modifier.height(14.dp))

                AuthField(
                    value = passwordConfirm,
                    onValueChange = { passwordConfirm = it },
                    placeholder = "Confirm new password",
                    keyboardType = KeyboardType.Password,
                    visualTransformation = PasswordVisualTransformation(),
                )

                if (state is AuthUiState.Error) {
                    Spacer(Modifier.height(12.dp))
                    Text((state as AuthUiState.Error).message, color = Color(0xFFEF4444), fontSize = 13.sp)
                }

                Spacer(Modifier.height(28.dp))

                Button(
                    onClick = { vm.resetPassword(email, otp, password, passwordConfirm) },
                    enabled = state !is AuthUiState.Loading,
                    modifier = Modifier.fillMaxWidth().height(52.dp),
                    shape = RoundedCornerShape(12.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = Accent, contentColor = Color.Black),
                ) {
                    if (state is AuthUiState.Loading) {
                        CircularProgressIndicator(color = Color.Black, modifier = Modifier.size(20.dp), strokeWidth = 2.dp)
                    } else {
                        Text("Reset Password", fontWeight = FontWeight.Bold, fontSize = 15.sp)
                    }
                }
            }
        }
    }
}
