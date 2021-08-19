## Installation

- `docker network create spreadsheet-icecream_vpc`
- `docker-compose up`
- API documentation: `https://documenter.getpostman.com/view/5999919/TzzANcaR`

## API Endpoints
- Upload File - `POST: https://localhost:8002/api/imports`
- Fetch All Imports - `GET: https://localhost:8002/api/imports`, this returns the import status (pending|completed|failed)
- Fetch All Contracts - `GET: https://localhost:8002/api/contracts`
- Fetch Contract - `GET: https://localhost:8002/api/contracts/:id`
- Fetch Contract Read Status - `GET: https://localhost:8002/api/contracts/:id/read-status`