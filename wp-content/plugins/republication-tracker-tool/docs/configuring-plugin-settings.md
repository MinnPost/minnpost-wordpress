# Configuring Plugin Settings

To configure the settings for the Republication Tracker Tool plugin, navigate to the `Settings` -> `Reading` inside of your WordPress admin panel once the plugin is installed
and activated.

![navigating to plugin settings](img/settings.png)

## Republication Tracker Tool Policy

The `Republication Tracker Tool Policy` field is where you will be able to input your rules and policies for users to see before they copy and paste your content to republish. A good example of a CCS policy can be found [here](https://www.propublica.org/steal-our-stories/).

![republication tracker tool policy field](img/republication-tracker-tool-policy.png)

## Republication Tracker Tool Google Analytics ID

In order to track your republished post pageviews in Google Analytics, you will need to insert your Google Analytics tracking ID into the field labeled `Republication Tracker Tool Google Analytics ID`.

![republication tracker tool google analytics id field](img/republication-tracker-tool-analytics-id.png)

To find your Google Analytics ID, first log into your Google Analytics account. Once logged in, click the `Admin` button on the bottom left corner.

![google analytics admin button](img/google-analytics-admin-button.png)

Once inside the admin panel, click into `Property Settings` for the property you want the tracking ID for.

![google analytics property settings](img/google-analytics-property-settings.png)

Inside of the `Property Settings` pane is where you will see your tracking ID.

![google analytics tracking id](img/google-analytics-tracking-id.png)

## Creative Commons Tracking Code

Once you have your Google Analytics ID set, you'll be able to view a sample tracking code that you will be able to copy and paste in order to manually insert into specific types of articles that you'd like to track. Just remember to swap out `YOUR-POST-ID` with the actual ID of the post you're going to insert the pixel into.

![republication tracker tool manual tracking code](img/republication-tracker-tool-tracking-code.png)

To find the ID of your post that you'd like to use, navigate to the edit page for the specific post. Once on the editor page, you can grab your post ID from the `post` parameter in the url.

![how to grab post id](img/find-post-id.png)

## Troubleshooting

### Why isn't my site logo appearing on republished posts?

The Republication Tracker Tool uses `get_site_icon_url` to grab your site favicon to display in republished posts. If your favicon is not appearing, please make sure your site has a favicon set in the way described in the [WordPress docs](https://wordpress.org/support/article/creating-a-favicon/).