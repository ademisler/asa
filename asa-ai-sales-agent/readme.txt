=== ASA AI Sales Agent ===
Contributors: ademisler
Tags: ai, chatbot, sales, gemini, google
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
ASA AI Sales Agent is a powerful WordPress plugin that integrates a smart AI chatbot into your website, powered by the Google Gemini API. This chatbot acts as a proactive sales assistant, engaging with your visitors, answering their questions, and guiding them through their journey on your site.

**Key Features:**
*   **Google Gemini Integration:** Leverage the advanced capabilities of Google's Gemini AI for intelligent conversations.
*   **Proactive Messaging:** Automatically initiates conversations with insightful questions based on the content of the page the user is viewing.
*   **Customizable Appearance:** Adjust the chatbot's title, subtitle, primary color, and avatar (icon or image) to match your brand.
*   **Chat History:** Maintains chat history in the user's local storage for a seamless experience.
*   **Admin Settings:** Easy-to-use settings page to configure API key, system prompt, and appearance options.
*   **API Key Test:** Test your Gemini API key directly from the settings page to ensure proper connection.
*   **Internationalization Ready:** Fully translatable to support multiple languages.

Enhance your customer engagement and boost your sales with an intelligent AI assistant that's always ready to help!

== Installation ==
1.  **Upload the plugin files** to the `/wp-content/plugins/asa-ai-sales-agent` directory, or install the plugin through the WordPress plugins screen directly.
2.  **Activate the plugin** through the 'Plugins' screen in WordPress.
3.  **Go to `Settings > ASA AI Sales Agent`** to configure your Gemini API Key and customize the chatbot's appearance and behavior.
4.  **Add the shortcode `[asa_chatbot]`** to any page or post where you want the chatbot to appear. The plugin can also insert the bot automatically on selected page types via the settings page.

== Frequently Asked Questions ==

= Where can I get a Google Gemini API Key? =
You can obtain your Google Gemini API Key from the [Google AI Studio](https://aistudio.google.com/app/apikey). It's a quick and easy process to generate a new key.

= Is this plugin free? =
Yes, the ASA AI Sales Agent plugin is 100% free to use. While the plugin itself is free, usage of the Google Gemini API may incur costs depending on your usage and Google's pricing policies. Please refer to Google's official documentation for details on API usage and pricing.

== Screenshots ==
1. General Settings Tab
2. Appearance Settings Tab
3. Behavior Settings Tab
4. Chatbot on Frontend

== Changelog ==

= 1.0.3 =
* Added API key testing functionality in settings.
* Improved API error handling for non-200 responses.

= 1.0.2 =
* Added option to disable automatic footer injection and choose page types.
* Implemented API error logging to `error.log`.
* Limited stored chat history with configurable limit.

= 1.0.1 =
* Added DOMPurify sanitization for AI responses.
* Added uninstall script to clean plugin data on deletion.


= 1.0.0 =
*   Initial Release.
*   Added GPLv2 License information.
*   Improved API error handling with more descriptive messages.
*   Implemented API Key Test feature in admin settings.
*   Added confirmation dialog for clearing chat history.
*   Prepared for Internationalization (i18n) with text domain loading and string wrapping.
*   Created readme.txt for WordPress plugin repository.
*   Prepared /languages and /assets directories.
