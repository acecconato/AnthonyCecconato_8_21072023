```mermaid
sequenceDiagram
    Client->>+Application: Request "Completed tasks" page
    Application->>Application: Authentication check
    Application->>+Database: Get user completed tasks
    Database-->>-Application: Returns completed tasks
    Application-->>-Client: Show "Completed tasks" page
```
