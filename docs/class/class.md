```mermaid
classDiagram
    class User {
        -Ulid id
        -String username
        -String email
        -String password
        -String plainPassword
        -String role
        
        +eraseCredentials(): void
    }

    class Task {
        -Ulid id
        -User owner
        -String title
        -String content
        -Boolean completed
    }

    User "0..1" -- "0..n" Task
    