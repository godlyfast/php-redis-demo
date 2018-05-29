# php-stats-demo

`docker-compose up`

POST http://localhost:8080/data

- used for registering Events, required fields are: country and event

- acceptable events are 'click', 'view', 'play'

`curl --request POST \
  --url http://localhost:8080/data \
  --header 'Cache-Control: no-cache' \
  --header 'Content-Type: application/json' \
  --data '{"country": "USA", "event": "click"}'`

GET http://localhost:8080/data

- used for displaying countries statistics: contain the sum of each event
over the last 7 days by country for the top 5 countries of all times

`curl --request GET \
  --url http://localhost:8080/data \
  --header 'Cache-Control: no-cache' \
  --header 'Content-Type: application/json' \`
