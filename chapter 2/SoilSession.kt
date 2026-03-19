fun main(){
    //here are farmer information val because they do not change and the session information is also val because it is fixed for this session
    val farmerName: String = "Mwana"
    val farmerLocation: String = "Mbeya -Field B"
    val sessionDate: String = "2025-01015"

    //sensor readings are var because they can change during the session
    var rawPhReading: Double = 5.8
    var calibrationOffset: Float = 0.1f
    var isBluetoothConnected: Boolean = false

    //calculatedPh
    val adjustedPh: Double = rawPhReading + calibrationOffset

    //string template embeded variables inside string with $ sign
    println("Farmer: $farmerName $farmerLocation")
    println("Date: $sessionDate")
    println("Adjusted pH: $adjustedPh")
    println("Bluetooth: ${if (isBluetoothConnected) "Connected" else "Disconnected"}")
}