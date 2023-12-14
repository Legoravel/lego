## Bug & Issue Reports

To encourage active collaboration, Lego strongly encourages contribution through [pull requests](#which-branch-and-how-to-contribute).
"Bug reports" may be searched or created in [issues](https://github.com/legoravel/lego/issues) or sent in the form of a [pull request](#which-branch-and-how-to-contribute) containing a failing test or steps to reproduce the bug.

If you file a bug report, your issue should contain a title and a clear description of the issue. You should also include as much relevant information as possible and a code sample that demonstrates the issue. The goal of a bug report is to make it easy for yourself - and others - to replicate the bug and develop a fix.

⏱  PRs and issues are usually checked about three times a week so there is a high chance yours will be picked up soon.

The Lego Architecture source code is on GitHub as [legoravel/lego](https://github.com/legoravel/lego).

## Support Questions

Lego Architecture's GitHub issue trackers are not intended to provide help or support. Instead, use one of the following channels:

- [Discussions](https://github.com/legoravel/lego/discussions) is where most conversations takes place
- For a chat hit us on our official [Slack workspace](https://lego-slack.herokuapp.com/) in the `#support` channel
- If you prefer StackOverflow to post your questions you may use [#legoravel](https://stackoverflow.com/questions/tagged/legoravel) to tag them

## Core Development Discussion

You may propose new features or improvements of existing Lego Architecture behaviour in the [Lego Discussins](https://github.com/legoravel/lego/discussions).
If you propose a new feature, please be willing to implement at least some of the code that would be needed to complete the feature, or collaborate on active ideation in the meantime.

Informal discussion regarding bugs, new features, and implementation of existing features takes place in the `#internals` channel of the [Lego Slack workspace](https://lego-slack.herokuapp.com/).
Aboozar Ghaffari, the maintainer of Lego, is typically present in the channel on weekdays from 8am-5pm EEST (Eastern European Summer Time), and sporadically present in the channel at other times.

## Which Branch? And How To Contribute

The `main` branch is what contains the latest live version and is the one that gets released.

- Fork this repository
- Clone the forked repository to where you'll edit your code
- Create a branch for your edits (e.g. `feature/queueable-units`, `fix/issue-31`)
- Commit your changes and their tests (if applicable) with meaningful short messages
- Push your branch `git push origin feature/queueable-units`
- Open a [PR](https://github.com/legoravel/lego/compare) to the `main` branch, which will run tests for your edits

⏱ PRs and issues are usually checked about three times a week.


### Setup for Development

Following are the steps to setup for development on Lego:

> Assuming we're in `~/dev` directory...

- Clone the forked repository `[your username]/lego` which will create a `lego` folder at `~/dev/lego`
- Create a Laravel project to test your implementation in it `composer create-project laravel/laravel myproject`
- Connect the created Laravel project to the local Lego installation; in the Laravel project's `composer.json`
    ```json
    "require": {
        "...": "",
        "legoravel/lego": "@dev"
    },
    "repositories": [
        {
            "type": "path",
            "url": "~/dev/lego",
            "options": {
                "symlink": true
            }
        }
    ],
    "minimum-stability": "dev",
    ```
> Make sure you change the `url` to the absolute path of your directory

- Run `composer update` to create the symlink

Now all your changes in the lego directory will take effect automatically in the project.

## Security Vulnerabilities

If you discover a security vulnerability within Lego, please send an email to Aboozar Ghaffari at [aboozar.ghf@gmail.com](mailto:aboozar.ghf@gmail.com).
All security vulnerabilities will be promptly addressed.

## Coding Style

Lego Architecture follows the [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) coding standard and the [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) autoloading standard.

### PHPDoc

Below is an example of a valid Lego Architecture documentation block. Note that the `@param` attribute is followed by two spaces, the argument type, two more spaces, and finally the variable name:

```php
/**
 * Register a binding with the container.
 *
 * @param  string|array  $abstract
 * @param  \Closure|string|null  $concrete
 * @param  bool  $shared
 * @return void
 *
 * @throws \Exception
 */
public function bind($abstract, $concrete = null, $shared = false)
{
    //
}
```
