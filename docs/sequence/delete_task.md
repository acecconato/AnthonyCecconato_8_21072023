```mermaid
sequenceDiagram
    Client->>+Application: Request "Delete task"
    Application->>Database: Check task owner
    Database-->>Application: Returns result
    alt Is not the task owner
        Application-->>Client: Show 404 error
    else
        Application->>Database: Delete the task
        Application->>Application: Add success flash message
        Application-->>-Client: Redirect to the previous page
        Client-)Client: Show success flash message
    end
```
