
//simulates the bluetooth services returning raw data
fun readBluetoothSensor(): String? {
    return if (Math.random() > 0.3)  "pH:6.45\n" else null
}
fun processSensorData(rawData: String?): String {
    //step 1 check for null (sensor disconected)
    if(rawData == null) return " Sensor Disconected. Please reconnect the sensor."

    //Step 2: clean and parse the data
    val cleaned = rawData.trim().removePrefix("pH:")
    val ph = cleaned.toDoubleOrNull()

    //step 3: validate the pH value
    if(ph == null || ph !in 0.0..14.0){
        return "invalid reading: '$cleaned'. Expected 0 - 14"
    }

    //step 4" generate recommendations
    return when {
        ph < 5.5 -> "pH $ph -Highly acidic. Apply lime urgently!"
        ph <= 6.8 -> "ph $ph - Ideal for beans"
        else -> "ph $ph - Alkaline. Consider adding sulfur to lower pH."
    }

}

fun main(){
    repeat(3){
        val data = readBluetoothSensor()
        println(processSensorData(data))
    }
    repeat(5){
        i ->
        println("Iteration $i")
    }
}