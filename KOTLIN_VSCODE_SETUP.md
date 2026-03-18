# How to Start Coding Kotlin in Visual Studio Code

This guide will walk you through setting up Kotlin programming in Visual Studio Code.

---

## 🚀 How to Run Your Kotlin Program (Quick Start)

Since you already have `first.kotlin` created, here's how to run it:

### Option 1: Run from Terminal (Recommended for Beginners)

**Step 1: Open Terminal**
- In VSCode: Press `` Ctrl+` `` (backtick) or go to **Terminal > New Terminal**

**Step 2: Check if Kotlin is installed**
```
bash
kotlinc -version
```
If you get "command not found", install Kotlin first (see "Install Kotlin Compiler" section below).

**Step 3: Run your program**
```
bash
kotlin first.kotlin
```

That's it! You should see the output:
```
Hello, World!
Welcome to Kotlin Programming!
My name is Developer and I am 25 years old
10 + 20 = 30
```

### Option 2: Compile and Run (Creates a JAR file)

**Compile the program:**
```
bash
kotlinc first.kotlin -include-runtime -d first.jar
```

**Run the compiled program:**
```
bash
java -jar first.jar
```

### Option 3: Using VSCode Code Runner Extension

1. Install "Code Runner" extension in VSCode (Ctrl+Shift+X, search "Code Runner")
2. Right-click on `first.kotlin`
3. Select "Run Code" or press **Ctrl+Alt+N**

---

## ⚠️ "Command not found" Error? Install Kotlin First!

If you get `kotlin: command not found` or `kotlinc: command not found`, you need to install Kotlin. Run these commands in your terminal:

### On Ubuntu/Debian (Recommended - Latest Version):
```
bash
# Install Kotlin using SDKMAN (recommended)
curl -s "https://get.sdkman.io" | bash
source "$HOME/.sdkman/bin/sdkman-init.sh"
sdk install kotlin

# Verify installation
kotlin -version
```

### Alternative - Install via Snap:
```
bash
sudo snap install kotlin
```

### Alternative - Install via APT (Older Version):
```
bash
sudo apt update
sudo apt install kotlin
```

After installation, verify with:
```
bash
kotlin -version
kotlinc -version
```

**Then run your program:**
```
bash
kotlin first.kotlin
```

---

## Prerequisites

### 1. Install Java Development Kit (JDK)
Kotlin runs on the JVM (Java Virtual Machine), so you need Java installed.

**On Linux (Ubuntu/Debian):**
```
bash
sudo apt update
sudo apt install openjdk-17-jdk
```

**On macOS:**
```
bash
brew install openjdk@17
```

**On Windows:**
- Download JDK 17 from: https://adoptium.net/
- Run the installer and follow the instructions
- Set JAVA_HOME environment variable

Verify Java installation:
```
bash
java -version
```

## Step 1: Install Visual Studio Code

If you don't have VSCode installed:

**On Linux (Ubuntu/Debian):**
```
bash
sudo apt install code
```

**On macOS:**
```
bash
brew install --cask visual-studio-code
```

**On Windows:**
- Download from: https://code.visualstudio.com/
- Run the installer

## Step 2: Install Kotlin Extension for VSCode

1. Open Visual Studio Code
2. Go to Extensions (Ctrl+Shift+X or Cmd+Shift+X)
3. Search for "Kotlin" 
4. Install the following extensions:
   - **Kotlin** (by mathiasfrosin) - Language support for Kotlin
   - **Kotlin Compiler** (optional) - Run Kotlin directly from VSCode

## Step 3: Install Kotlin Compiler

To compile and run Kotlin programs, you need the Kotlin compiler.

**On Linux (Ubuntu/Debian):**
```
bash
# Download Kotlin compiler
curl -s https://get.sdkman.io | bash
source "$HOME/.sdkman/bin/sdkman-init.sh"
sdk install kotlin

# OR using apt
sudo apt install kotlin
```

**On macOS:**
```
bash
brew install kotlin
```

**On Windows:**
- Download from: https://github.com/JetBrains/kotlin/releases
- Extract to a folder (e.g., C:\kotlin)
- Add to PATH environment variable

Verify Kotlin installation:
```
bash
kotlinc -version
```

## Step 4: Create Your First Kotlin Program

1. Create a new folder for your project
2. Open the folder in VSCode (File > Open Folder)
3. Create a new file named `HelloWorld.kt`
4. Add the following code:

```
kotlin
fun main() {
    println("Hello, World!")
    println("Welcome to Kotlin Programming!")
    
    // Variables
    val name = "Developer"
    val age = 25
    
    // Print with string template
    println("My name is $name and I am $age years old")
    
    // Function example
    val result = add(10, 20)
    println("10 + 20 = $result")
}

fun add(a: Int, b: Int): Int {
    return a + b
}
```

## Step 5: Run Your Kotlin Program

### Option 1: Using Kotlin Compiler (Terminal)

**Compile and run:**
```
bash
kotlinc HelloWorld.kt -include-runtime -d HelloWorld.jar
java -jar HelloWorld.jar
```

**Or run directly:**
```
bash
kotlin HelloWorld.kt
```

### Option 2: Using VSCode Extension

1. Install "Code Runner" extension in VSCode
2. Right-click on the Kotlin file
3. Select "Run Code" (or press Ctrl+Alt+N)

## Step 6: Setting Up Debugging (Optional)

For debugging Kotlin in VSCode:

1. Install "Java Debugger" extension
2. Configure launch.json for debugging

## Project Structure Example

```
my-kotlin-project/
├── HelloWorld.kt
├── utils/
│   └── MathUtils.kt
└── README.md
```

## Useful VSCode Shortcuts

| Action | Windows/Linux | macOS |
|--------|---------------|-------|
| Run Code | Ctrl+Alt+N | Cmd+Alt+N |
| Format Code | Shift+Alt+F | Shift+Option+F |
| Quick Fix | Ctrl+. | Cmd+. |
| Terminal | Ctrl+` | Cmd+` |

## Troubleshooting

### "kotlinc: command not found"
- Make sure Kotlin is in your PATH
- Restart your terminal or VSCode

### "Java not found"
- Install JDK and set JAVA_HOME
- VSCode may need to be restarted

### Extension not working
- Reload VSCode: Ctrl+Shift+P > "Developer: Reload Window"
- Check extension settings

## Next Steps

- Learn Kotlin basics: https://kotlinlang.org/docs/basic-syntax.html
- Practice with Kotlin Koans: https://kotlinlang.org/docs/koans.html
- Explore Android development with Kotlin

## Quick Reference Commands

```
bash
# Compile Kotlin file
kotlinc filename.kt

# Run Kotlin file directly
kotlin filename.kt

# Compile to JAR
kotlinc filename.kt -include-runtime -d filename.jar

# Run JAR
java -jar filename.jar
```

---

**Happy Coding! 🎉**
