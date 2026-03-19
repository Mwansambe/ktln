fun main(){
    val sensorData: String? = getSensorData()
    val display = sensorData ?: "Sensor data not connected"
val dataLength = sensorData?.length ?: 0
println("Sensor data length: $dataLength")
val rawInput: String? = getSensorData()
val ph: Double? = rawInput?.toDoubleOrNull() ?: 7.0
println("pH value: $ph")
}
fun getSensorData(): String? {
    // Simulate a sensor reading that might be null
    return if (Math.random() > 0.5) "Sensor Reading" else null
}