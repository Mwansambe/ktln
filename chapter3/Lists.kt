//immutable list can not add o remove items
val soilTips = listOf(
    "Test soil every season before planting",
    "apply lime 2-3 weeks before planting",
    "Ideal for beans: 6.2 to 6.8 ",
    "Dolomite lime also adds calciuma and magnesium"
)


fun main(){

println(soilTips.size)
println(soilTips.last())
// mutablelist add or remove items
val phReadings = mutableListOf(5.8, 6.2, 7.1)
phReadings.add(2.3)
}