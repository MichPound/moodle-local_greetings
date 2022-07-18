@brickfield @local_greetings
Feature: greetings
        Background:
            Given the following "users" exist:
                  | username | firstname | lastname | email             | country |
                  | student1 | John      | Smith    | user1@example.com | AU      |
                  | student2 | Jean      | Smith    | user2@example.com | ES      |

        Scenario: Test AU hello message
              And I log in as "student1"
              And I follow "Greetings"
             Then I should see "Hello John Smith!"

        Scenario: Test ES hello message
              And I log in as "student2"
              And I follow "Greetings"
             Then I should see "Hola Jean Smith!"