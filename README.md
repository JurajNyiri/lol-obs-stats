# lol-obs-stats

Add via Browser input to your stream. Display your last game, win rate, current elo and more.

![example look](https://github.com/JurajNyiri/lol-obs-stats/blob/main/img/example.jpeg?raw=true)

# Installation

Rename config.sample.php to config.php and set all variables.

Open /?`yourHTTPPassword`

## Optional URL parameters

- `api_key` - override api_key set in config.php via URL
- `champion` - get stats only for specific champion you played
- `gameAfter` - get only games after UNIX timestamp with ms, example value: 1641575632000
- `design` - specify template to use

# Configuration

You can create new templates in template folder. You have 2 objects available:

- `$error` can have `message` and `progress` attributes. If `message` is nonempty, you need to display error message on your template.
- `$data` can have a lot of data in it. Check index.php for all available data to display. You can also `var_dump` it and see.

All data gathering is done in index.php.

You can then use `&design` parameter in URL set to your folder name to use your design.

# Final note

This was put together very quickly and is in no way a nice or scalable code. It will do the job and that was enough for me on this project.
