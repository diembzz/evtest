## Usage

To install and run the application:

```
git clone git@github.com:diembzz/evtest.git
cd evtest
cp .env.example .env
docker-compose up
```

Wait for the evtest-app container to load.\
This will be indicated by the output in the console:

```
evtest-app    | [12-Jul-2024 17:59:14] NOTICE: fpm is running, pid 1
evtest-app    | [12-Jul-2024 17:59:14] NOTICE: ready to handle connections
```

After that the following url will be available:

* Frontend: http://localhost
* API:
  - http://localhost/api/v1/events
  - http://localhost/api/v1/venues
