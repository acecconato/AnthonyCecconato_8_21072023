```mermaid
sequenceDiagram
    Client->>+Application: Request "Add a new task" page
    Application->>Application: Authentication check
    Application-->>-Client: Show "Add a new task" page
    Client->>Client: Fill in the form

    Client->>+Application: Validate and send form data
    Application->>Application: Authentication check
    Application->>Application: Check data sent
    Application->>Database: Save the new task
    Application->>Application: Add success flash message
    Application-->>-Client: Redirect to task list page
    Client-)Client: Show success flash message
```
