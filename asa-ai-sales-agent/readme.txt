=== Ai Sales Agent (ASA) ===
Contributors: ademisler
Tags: ai, chatbot, sales, gemini, google, artificial intelligence, customer support, lead generation, conversational ai, proactive messaging
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Transform your website into a sales powerhouse with an intelligent AI chatbot powered by Google Gemini that proactively engages visitors and drives conversions.

== Description ==

**Ai Sales Agent (ASA)** is a cutting-edge WordPress plugin that revolutionizes how you engage with website visitors. Powered by Google's advanced Gemini AI, this intelligent chatbot doesn't just wait for questions—it proactively initiates meaningful conversations based on the content your visitors are viewing.

### 🚀 **Key Features**

**🤖 Advanced AI Technology**
* **Google Gemini Integration:** Harnesses the power of Google's latest Gemini 2.0 Flash model for intelligent, context-aware conversations
* **Context-Aware Responses:** Understands page content to provide relevant, personalized interactions
* **Natural Language Processing:** Delivers human-like conversations that feel natural and engaging

**💡 Proactive Engagement**
* **Smart Proactive Messaging:** Automatically generates contextual conversation starters based on page content
* **Intelligent Timing:** Customizable delay settings to engage visitors at the optimal moment
* **Content Analysis:** Analyzes page content to create relevant, insightful opening questions
* **Session Management:** Remembers dismissed messages to avoid being intrusive

**🎨 Complete Customization**
* **Visual Branding:** Match your brand with customizable colors, titles, and subtitles
* **Avatar Options:** Choose from 50+ FontAwesome icons or upload custom images
* **Positioning Control:** Place the chatbot on left or right side of your website
* **Responsive Design:** Optimized for desktop, tablet, and mobile devices
* **Full-Screen Mobile:** Enhanced mobile experience with full-screen chat interface

**📊 Smart Features**
* **Chat History Management:** Persistent chat history stored locally with configurable limits
* **API Key Testing:** Built-in tool to verify your Gemini API connection
* **Error Handling:** Comprehensive error management with user-friendly messages
* **Performance Optimization:** Cached proactive messages and optimized API calls
* **Security First:** Sanitized inputs, nonce verification, and secure API communication

**⚙️ Flexible Display Options**
* **Shortcode Support:** Use `[asa_chatbot]` anywhere on your site
* **Auto-Insertion:** Automatically display on selected page types
* **Page Type Control:** Choose specific pages, posts, archives, or front page
* **Manual Control:** Complete control over where and when the chatbot appears

**🌍 Developer & User Friendly**
* **Internationalization Ready:** Full translation support with included .pot file
* **Accessibility Compliant:** ARIA labels, keyboard navigation, and screen reader support
* **Clean Uninstall:** Removes all data completely when uninstalled
* **WordPress Standards:** Follows WordPress coding standards and best practices

### 🎯 **Perfect For**

* **E-commerce Sites:** Guide customers through product selection and increase sales
* **Service Businesses:** Qualify leads and schedule consultations
* **Content Sites:** Engage readers and reduce bounce rates
* **SaaS Companies:** Provide instant support and demo scheduling
* **Educational Sites:** Answer student questions and guide course selection
* **Any Website:** Improve user engagement and conversion rates

### 🔧 **Technical Specifications**

* **AI Model:** Google Gemini 2.0 Flash
* **Frontend:** Vanilla JavaScript with jQuery
* **Styling:** Modern CSS with FontAwesome icons
* **Security:** WordPress nonces, input sanitization, and secure API calls
* **Performance:** Lightweight footprint with optimized loading
* **Compatibility:** Works with all modern WordPress themes

### 📈 **Benefits**

* **Increase Conversions:** Proactive engagement leads to higher conversion rates
* **Reduce Bounce Rate:** Keep visitors engaged with intelligent conversations
* **24/7 Availability:** Your AI sales agent never sleeps
* **Scalable Solution:** Handle unlimited conversations simultaneously
* **Cost-Effective:** Reduce support costs while improving customer experience
* **Easy Setup:** Get started in minutes with simple configuration

== Privacy and AI Disclaimer ==

**Important Privacy Information:**
This plugin sends visitor messages and page content to Google's Gemini API to generate intelligent responses. By using this plugin, you acknowledge that:

* User messages and page content are transmitted to Google's servers for processing
* Google may process this data according to their terms of service and privacy policy
* AI-generated responses may occasionally be inaccurate, biased, or inappropriate
* You should review and moderate AI responses as needed for your specific use case
* Consider informing your users about AI-powered chat functionality in your privacy policy

**Recommended:** Review Google's AI API terms of service and ensure compliance with applicable privacy regulations (GDPR, CCPA, etc.) in your jurisdiction.

== Installation ==

### 🚀 **Quick Setup (5 Minutes)**

**Step 1: Install the Plugin**
1. Upload the plugin files to `/wp-content/plugins/asa-ai-sales-agent/` directory
2. Or install directly through WordPress Admin → Plugins → Add New → Search "Ai Sales Agent (ASA)"
3. Activate the plugin through the 'Plugins' screen in WordPress

**Step 2: Get Your Google Gemini API Key**
1. Visit [Google AI Studio](https://aistudio.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key" and copy the generated key
4. Note: Google Gemini API offers generous free tier limits

**Step 3: Configure the Plugin**
1. Go to WordPress Admin → Settings → Ai Sales Agent (ASA)
2. Paste your API Key in the "Gemini API Key" field
3. Click "Test API Key" to verify connection
4. Customize appearance settings (title, colors, avatar)
5. Configure behavior settings (proactive messaging, display options)
6. Save settings

**Step 4: Display the Chatbot**
* **Automatic:** Enable "Auto Insert" to display on selected page types
* **Manual:** Use shortcode `[asa_chatbot]` on specific pages/posts
* **Template:** Add `<?php echo do_shortcode('[asa_chatbot]'); ?>` to theme files

### 📋 **Detailed Configuration**

**General Settings:**
* **API Key:** Your Google Gemini API key (required)
* **System Prompt:** Customize the AI's personality and behavior
* **Test Connection:** Verify API key functionality

**Appearance Settings:**
* **Title:** Chatbot header title (e.g., "Sales Assistant")
* **Subtitle:** Secondary text (e.g., "How can I help you?")
* **Primary Color:** Brand color for buttons and accents
* **Avatar:** Choose icon or upload custom image
* **Position:** Left or right side positioning

**Behavior Settings:**
* **Auto Insert:** Enable automatic chatbot display
* **Display Types:** Choose where to show (everywhere, specific page types)
* **Proactive Delay:** Time before showing proactive message (milliseconds)
* **History Limit:** Maximum stored chat messages per user
* **Show Credit:** Display "Powered by" attribution

== Frequently Asked Questions ==

= Where can I get a Google Gemini API Key? =

Visit [Google AI Studio](https://aistudio.google.com/app/apikey) to generate your free API key. Google offers generous free tier limits that are suitable for most websites. The process takes less than 2 minutes.

= Is this plugin completely free? =

Yes! The Ai Sales Agent (ASA) plugin is 100% free with no premium versions or hidden costs. However, the Google Gemini API has usage-based pricing after the free tier limits. Most small to medium websites stay within the free tier.

= What are the Google Gemini API costs? =

Google Gemini API offers a generous free tier with millions of tokens per month. Paid usage is very affordable, typically costing pennies per thousand interactions. Check [Google's pricing page](https://ai.google.dev/pricing) for current rates.

= Can I customize the chatbot's personality? =

Absolutely! Use the "System Prompt" setting to define your AI's personality, knowledge base, and behavior. You can make it professional, friendly, technical, or match any brand voice you prefer.

= Does the chatbot work on mobile devices? =

Yes! The chatbot is fully responsive with a special full-screen mobile interface that provides an optimal experience on smartphones and tablets.

= Can I control where the chatbot appears? =

Yes! You have complete control:
* Use shortcode `[asa_chatbot]` for specific locations
* Enable auto-insertion for selected page types
* Choose front page, posts, pages, archives, or everywhere
* Disable on specific pages as needed

= How does proactive messaging work? =

The AI analyzes your page content and generates contextual conversation starters. For example, on a product page, it might ask "Interested in learning more about this product?" These messages appear after a customizable delay and can be dismissed by users.

= Is the chatbot accessible? =

Yes! The plugin includes full accessibility features:
* ARIA labels and roles
* Keyboard navigation support
* Screen reader compatibility
* Focus management and trapping
* High contrast support

= Can I translate the plugin? =

Yes! The plugin is fully internationalization-ready with a complete .pot translation file included. You can translate all user-facing text into any language.

= What happens to chat data? =

Chat history is stored locally in users' browsers using localStorage. No chat data is stored on your server or sent to third parties (except for AI processing via Google's API). Users can clear their own chat history anytime.

= How do I troubleshoot connection issues? =

1. Use the built-in "Test API Key" feature in settings
2. Check that your API key is correct and active
3. Verify your website can make external HTTP requests
4. Check browser console for JavaScript errors
5. Ensure your hosting provider allows Google API connections

= Can I modify the chatbot's appearance? =

Yes! You can customize:
* Colors and branding
* Title and subtitle text
* Avatar (50+ icons or custom images)
* Position (left or right side)
* Mobile behavior and styling

= Does this work with my theme? =

Yes! The plugin is designed to work with any properly coded WordPress theme. It uses absolute positioning and doesn't interfere with your theme's layout or styling.

= How do I report bugs or request features? =

Visit the [plugin support forum](https://wordpress.org/support/plugin/asa-ai-sales-agent/) or contact the developer through the plugin settings page. Bug reports and feature requests are always welcome!

== Screenshots ==

1. **General Settings Tab** - Configure your Google Gemini API key and test connection
2. **Appearance Settings Tab** - Customize colors, title, avatar, and positioning  
3. **Behavior Settings Tab** - Control proactive messaging and display options
4. **Frontend Chatbot** - See how the chatbot appears to your website visitors
5. **Mobile Experience** - Full-screen mobile chat interface for optimal usability
6. **Proactive Messaging** - Context-aware conversation starters based on page content

== Changelog ==

= 1.0.7 - Latest Release =
**Enhanced User Experience**
* ✨ **Improved Proactive Messaging:** Smarter chat window opening with enhanced message handling
* 📱 **Better Mobile Layout:** Full-screen chat interface with improved pointer events and touch interactions
* 🎨 **Enhanced Welcome Bubble:** Improved styling and readability for proactive messages
* 🧹 **Code Optimization:** Simplified logging system and cleaner uninstall routine
* 🔧 **Performance Improvements:** Optimized JavaScript execution and reduced resource usage

= 1.0.6 - Critical Fixes =
**Bug Fixes & Stability**
* 🐛 **Fixed Critical Issue:** Resolved missing Showdown script that prevented chat window from opening
* 🔧 **Improved JSON Handling:** Enhanced chat history parsing with better error handling
* ⚙️ **Better Defaults:** Added proper default values for title, subtitle, and primary color on activation
* 📁 **Asset Management:** Fixed missing newline characters in asset files
* 🛡️ **Error Prevention:** Added fallbacks for undefined configuration values

= 1.0.5 - Proactive Messaging Update =
**New Features**
* ⏱️ **Proactive Delay Control:** Added option to customize proactive message timing
* 💭 **Smart Message Management:** Proactive message dismissal now remembered per browser session
* 📱 **Responsive Improvements:** Enhanced mobile and tablet layouts for chat window and admin settings
* 🎯 **User Experience:** Better handling of proactive message display and interaction

= 1.0.4 - Accessibility & UX =
**Accessibility Enhancements**
* ♿ **Full Accessibility Support:** Added comprehensive ARIA roles and labels
* ⌨️ **Keyboard Navigation:** Complete keyboard support with focus trapping
* 🎨 **Motion Sensitivity:** Added support for users with reduced-motion preferences
* ⚠️ **Color Contrast:** Warning system for low contrast color combinations
* 🔧 **UX Improvements:** Enhanced user interface interactions and feedback

= 1.0.3 - API Testing =
**Developer Features**
* 🧪 **API Key Testing:** Built-in tool to verify Google Gemini API connection
* 🔍 **Enhanced Error Handling:** Improved API error messages and debugging information
* 📊 **Connection Diagnostics:** Better feedback for API connection issues
* 🛠️ **Developer Tools:** Enhanced debugging capabilities for troubleshooting

= 1.0.2 - Display Control =
**Flexibility Improvements**
* 🎯 **Display Options:** Added granular control over where chatbot appears
* 📄 **Page Type Selection:** Choose specific page types (posts, pages, archives, front page)
* 📝 **Error Logging:** Implemented comprehensive API error logging to error.log
* 💾 **History Management:** Configurable chat history limits with automatic cleanup
* ⚙️ **Auto-Insert Control:** Option to disable automatic footer injection

= 1.0.1 - Security & Cleanup =
**Security Enhancements**
* 🛡️ **DOMPurify Integration:** Added sanitization for all AI responses to prevent XSS
* 🧹 **Clean Uninstall:** Comprehensive uninstall script removes all plugin data
* 🔒 **Security Hardening:** Enhanced input validation and output escaping
* 📦 **Data Management:** Proper cleanup of transients and cached data

= 1.0.0 - Initial Release =
**Core Features**
* 🚀 **Plugin Launch:** Initial public release with full functionality
* 📄 **GPLv2 License:** Open source license with complete legal compliance
* 🔑 **API Integration:** Full Google Gemini API integration with error handling
* 🧪 **Testing Tools:** Built-in API key validation and testing features
* 🗑️ **History Management:** Chat history clearing with user confirmation
* 🌍 **Internationalization:** Complete i18n support with text domain and translation files
* 📖 **Documentation:** Comprehensive readme.txt for WordPress plugin repository
* 📁 **Asset Organization:** Structured /languages and /assets directories for better organization

== Upgrade Notice ==

= 1.0.7 =
Enhanced proactive messaging and mobile experience. Recommended update for all users.

= 1.0.6 =
Critical bug fixes for chat functionality. Update immediately if experiencing chat issues.

= 1.0.5 =
New proactive messaging controls and responsive improvements. Recommended for better user experience.

== Support & Development ==

**🎯 Support**
* [WordPress Support Forum](https://wordpress.org/support/plugin/asa-ai-sales-agent/)
* [Plugin Documentation](https://wordpress.org/plugins/asa-ai-sales-agent/)

**💝 Show Your Appreciation**
If this plugin helps your business grow, consider [supporting development](https://buymeacoffee.com/ademisler) to keep updates and new features coming!

**🔧 For Developers**
* Clean, well-documented code following WordPress standards
* Extensive hooks and filters for customization
* Modern JavaScript with jQuery compatibility
* Comprehensive sanitization and security measures
* Full internationalization support

---

*Transform your website visitors into customers with intelligent, proactive AI engagement. Install ASA AI Sales Agent today and watch your conversions grow!*
