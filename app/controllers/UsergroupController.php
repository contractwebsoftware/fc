<?php
class UsergroupController extends BaseController
{
    public function getIndex(){
        try
        {
            // Create the group
            $group = Sentry::createGroup(array(
                'name'        => 'Provider',
                'permissions' => array(
                    'provider' => 1,
                ),
            ));
        }
        catch (Cartalyst\Sentry\Groups\NameRequiredException $e)
        {
            echo 'Name field is required';
        }
        catch (Cartalyst\Sentry\Groups\GroupExistsException $e)
        {
            echo 'Group already exists';
        }
    }
}