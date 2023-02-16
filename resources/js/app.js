require('./bootstrap');
mapboxgl.accessToken = 'pk.eyJ1Ijoiam9kaWFsIiwiYSI6ImNsMTN6ODZpMjBqcWEzaXM2bXQ0eGI1cjEifQ.wEXNx8MUVOiiximLzspJyQ';
const map = new mapboxgl.Map({
  container: 'map',
  style: 'mapbox://styles/mapbox/streets-v11',
  center: [-122.662323, 45.523751], // starting position
  zoom: 12
});
// set the bounds of the map
const bounds = [
  [-123.069003, 45.395273],
  [-122.303707, 45.612333]
];
map.setMaxBounds(bounds);

// an arbitrary start will always be the same
// only the end or destination will change
const start = [-122.662323, 45.523751];

// this is where the code for the next step will go
