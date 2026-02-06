style="text-transform: uppercase;"          ===> UPPERCASE
strtoupper({{ $patientData['last_name'] }}) ===> UPPERCASE
text-uppercase                              ===> UPPERCASE
text-transform: capitalize; ===> Upper Case

<input type="text" class="form-control" id="sentenceInput" placeholder="Type here..."> ===> Upper case. Upper case

document.getElementById("sentenceInput").addEventListener("input", function() {
let value = this.value.toLowerCase();
this.value = value.charAt(0).toUpperCase() + value.slice(1);
});