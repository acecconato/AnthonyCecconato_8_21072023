```mermaid
sequenceDiagram
    Client->>+Application: Request "Todo" page
    Application->>Application: Authentication check
    Application->>+Database: Get user tasks to complete
    Database-->>-Application: Returns tasks to complete
    Application-->>-Client: Show "Todo" page
```
