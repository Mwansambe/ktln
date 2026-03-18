# How to Run Kotlin Programs (.kt files)

This guide explains how to run Kotlin programs like **PhCalculator.kt** in this directory. Assumes your setup: **Kotlin 2.3.10** (SDKMAN), **Java 21** (OpenJDK), **VSCode** with Code Runner.

## Prerequisites (Already Set Up)
```
kotlin -version   # → 2.3.10
kotlinc -version  # → 2.3.10
java -version     # → OpenJDK 21
```

## For Any .kt File (e.g., PhCalculator.kt)

### 1. Direct Run (No Compile, Fastest)
```bash
cd /home/mwansambe/Desktop/kotlin/chapter\ 1
kotlin PhCalculator.kt
```
- Interactive if `readLine()` used.
- No JAR needed.

### 2. Compile to JAR & Run
```bash
cd /home/mwansambe/Desktop/kotlin/chapter\ 1
kotlinc PhCalculator.kt -include-runtime -d PhCalculator.jar
java -jar PhCalculator.jar
```
- Creates standalone executable JAR (like existing `PhCalculator.jar`).

### 3. VSCode Code Runner (One-Click)
- Open `PhCalculator.kt` in VSCode.
- Right-click → **Run Code** (`Ctrl+Alt+N`).
- Output in integrated terminal.

### 4. Terminal in VSCode
- `Ctrl+`` ` → New Terminal.
- Run commands above (auto in project dir).

## Example Output (PhCalculator.kt)
```
=============================
Soil pH Analyser - Version 1.0
=============================
Enter soil ph value (0-14): 6.5

Soil pH Analysis Result:
ph value: 6.5
Recorded at: 2024-...
Assessment: Ideal for beans - No treatment needed. Plant now.

Reading saved successfully!
```

## Troubleshooting
- **Command not found**: `source ~/.sdkman/bin/sdkman-init.sh`
- **No main manifest**: Use `-include-runtime` in `kotlinc`.
- **Permissions**: `chmod +x *.jar`
- **Reload VSCode**: `Ctrl+Shift+P` → Reload Window.

## Quick Commands for Any .kt
```bash
# Direct
kotlin yourfile.kt

# JAR
kotlinc yourfile.kt -include-runtime -d yourfile.jar && java -jar yourfile.jar
```

**Pro Tip**: For scripts with `main()`, direct `kotlin` works best.

Happy Coding! 🚀
