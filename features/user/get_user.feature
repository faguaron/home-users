Feature: Get user by ID
  As an API consumer
  I want to retrieve a user by their ID
  So that I can view their details

  Scenario: Get an existing user
    Given a user exists with alias "alice"
    When I request GET for user "alice"
    Then the response status code should be 200
    And the response should be a valid UserResponse

  Scenario: Get a non-existent user returns 404
    When I request GET for a non-existent user id
    Then the response status code should be 404
    And the response should be a valid error response

  Scenario: Get a user with invalid UUID returns 422
    When I request GET for an invalid UUID
    Then the response status code should be 422
    And the response should be a valid error response
