Feature: Delete user
  As an API consumer
  I want to delete a user by ID
  So that they are removed from the system

  Scenario: Delete an existing user
    Given a user exists with alias "alice"
    When I delete user "alice"
    Then the response status code should be 204
    And the response body should be empty

  Scenario: Delete a non-existent user returns 404
    When I delete a non-existent user
    Then the response status code should be 404
    And the response should be a valid error response

  Scenario: Delete a user with invalid UUID returns 422
    When I delete a user with an invalid UUID
    Then the response status code should be 422
    And the response should be a valid error response
