Feature: List users
  As an API consumer
  I want to list all users
  So that I can see who is registered

  Scenario: List users when none exist
    Given there are no users in the system
    When I request GET "/api/users"
    Then the response status code should be 200
    And the response should be an empty array

  Scenario: List users returns all registered users
    Given a user exists with alias "alice"
    And a user exists with alias "bob"
    When I request GET "/api/users"
    Then the response status code should be 200
    And the response should be an array of valid UserResponse objects
