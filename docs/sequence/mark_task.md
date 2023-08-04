```mermaid
sequenceDiagram
    Client->>+Application: "Mark a Task" request
    Application->>Database: Check task owner
    Database-->>Application: Returns result
    alt Is not the task owner
        Application-->>Client: Show 404 error
    else
        Application->>Database: Update the task status
        Application->>Application: Add success flash message
        Application-->>-Client: Redirect to the previous page
        Client-)Client: Show success flash message
    end
```
