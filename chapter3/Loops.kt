// for loop with a range
fun main(){
var stability = 0.0
var attempts = 0
while (stability < 95.0 && attempts < 10) {
    stability = readsensorstability()  // simulated function
    attempts++
    println("Stability: $stability% (attempt $attempts)")
}
println(if (stability >= 95.0) "✅ Reading stable" else "⚠️ Timeout")

}


fun readsensorstability(){
    println("processing 5 sensor readings")
for (i in 1..5) {
    println("Reading #$i")
}

// for loop with step
for (i in 0..100 step 10) {
    print("$i ")
}
for (i in 5 downTo 1) {
    println("Countdown: $i")
}

       

}

