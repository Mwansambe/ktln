fun main(){
    val soilType: String? = null
    println("Soil type: ${soilType?.length ?: "0"}")
}
/*

This Kotlin program demonstrates **null safety** with the Elvis operator (`?:`).  

1. A nullable variable `soilType` is declared and set to `null`.  
2. `soilType?.length` safely checks the length only if it’s not null.  
3. Since `soilType` is null, the Elvis operator provides a default value `"0"`.  
4. The output is:  
   ```
   Soil type: 0
   ```  

👉 In short, it shows how Kotlin avoids crashes by safely handling null values and giving a fallback.
*/