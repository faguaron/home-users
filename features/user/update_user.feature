Feature: Update user
  As an API consumer
  I want to update an existing user's data
  So that their record stays current

  Scenario: Update an existing user with valid data
    Given a user exists with alias "alice"
    When I update user "alice" with valid data
    Then the response status code should be 204
    And the response body should be empty

  Scenario: Update a non-existent user returns 404
    When I update a non-existent user
    Then the response status code should be 404
    And the response should be a valid error response

  Scenario: Update a user with duplicate email returns 422
    Given a user exists with alias "alice"
    And a user exists with alias "bob"
    When I update user "bob" changing email to match "alice"
    Then the response status code should be 422
    And the response should be a valid error response

  Scenario: Update a user with invalid DNI returns 422
    Given a user exists with alias "alice"
    When I update user "alice" with invalid DNI
    Then the response status code should be 422
    And the response should be a valid error response
