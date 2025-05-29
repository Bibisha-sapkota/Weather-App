async function getAndDisplayWeather(city) {

  let data;
  //check the browser is online
  if (navigator.onLine) {
    try {
      //fetch data from server
      const response = await fetch(`try.php?q=${city}`);
      data = await response.json();
//store fetch data in localstorage for offline use
      localStorage.setItem(city, JSON.stringify(data));
    } catch (error) {
      console.error('Error fetching data:', error);
      alert('Error fetching data from the server.');
      return;
    }
  } else {
    data = JSON.parse(localStorage.getItem(city));//fetch data from localstorage if offline

    if (!data) {
      alert('No cached data available for offline use.');
      return;
    }
  }
  console.log(data);

  //UI element with fetched data
  var place = document.getElementById("cityN");
  place.innerHTML = data[0].City;

  var tempDiv = document.getElementById('temp-div');
  tempDiv.innerHTML = data[0].Temperature + "℃";

  var humid = document.getElementById("humidity");
  humid.innerHTML = "Humidity: " + data[0].Humidity + "%";

  var wind = document.getElementById("wind");
  wind.innerHTML = "Wind speed: " + data[0].Wind + " km/h";

  var pressure = document.getElementById("pressure");
  pressure.innerHTML = "Pressure: " + data[0].Pressure + " hPa";

  var description = document.getElementById("Description");
  description.innerHTML = "Description:" + data[0].Description;

  var icon = document.getElementById("icons");
  
 
}



const formElement = document.getElementById('btn');
const inputElement = document.getElementById('cityName');
formElement.addEventListener('click', function (event) {
  event.preventDefault();
  const city = inputElement.value;
  getAndDisplayWeather(city);
});

getAndDisplayWeather("Dharan");

