```mermaid
sequenceDiagram
    Client->>+Application: Logout request
    Application->>Application: Destroy the user session
    Application->>Application: Add success flash message
    Application-->>-Client: Redirect to the login page
    Client-)Client: Show success flash message     
```
