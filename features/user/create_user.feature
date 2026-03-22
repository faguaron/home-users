Feature: Create user
  As an API consumer
  I want to create a new user
  So that they are registered in the system

  Scenario: Create a user with valid data
    When I create a user with valid data and alias "alice"
    Then the response status code should be 201
    And the response should contain the id for user "alice"

  Scenario: Create a user fails when email already exists
    Given a user exists with alias "alice"
    When I create a user with the same email as "alice"
    Then the response status code should be 422
    And the response should be a valid error response

  Scenario: Create a user fails with invalid DNI
    When I create a user with invalid DNI
    Then the response status code should be 422
    And the response should be a valid error response
