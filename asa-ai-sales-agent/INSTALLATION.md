# Ai Sales Agent (ASA) - Installation & Setup Guide 🚀

This comprehensive guide will walk you through the complete installation and configuration process for Ai Sales Agent (ASA), from initial setup to customization.

## 📋 Prerequisites

Before installing Ai Sales Agent (ASA), ensure your environment meets these requirements:

### System Requirements
- **WordPress**: Version 5.0 or higher ✅
- **PHP**: Version 7.4 or higher ✅
- **MySQL**: Version 5.6 or higher (or MariaDB 10.1+) ✅
- **HTTPS**: Recommended for secure API communication 🔒

### Required Accounts
- **Google Account**: For accessing Google AI Studio and generating API keys
- **WordPress Admin Access**: To install and configure the plugin

### Browser Compatibility
- **Modern Browsers**: Chrome 70+, Firefox 65+, Safari 12+, Edge 79+
- **Mobile**: iOS Safari 12+, Chrome Mobile 70+

---

## 🎯 Step 1: Plugin Installation

### Method 1: WordPress Admin Dashboard (Recommended)

1. **Login to WordPress Admin**
   ```
   Navigate to: yoursite.com/wp-admin
   ```

2. **Access Plugin Installation**
   ```
   Go to: Plugins → Add New
   ```

3. **Search for Plugin**
   ```
       Search: "Ai Sales Agent (ASA)"
   ```

4. **Install & Activate**
   ```
   Click: "Install Now" → "Activate"
   ```

### Method 2: Manual Upload

1. **Download Plugin**
   - Download the plugin ZIP file from WordPress.org
   - Or obtain it from the developer

2. **Upload via WordPress Admin**
   ```
   Plugins → Add New → Upload Plugin → Choose File → Install Now
   ```

3. **Activate Plugin**
   ```
   Plugins → Installed Plugins → Ai Sales Agent (ASA) → Activate
   ```

### Method 3: FTP Upload (Advanced)

1. **Extract Files**
   ```bash
   unzip asa-ai-sales-agent.zip
   ```

2. **Upload via FTP**
   ```
   Upload to: /wp-content/plugins/asa-ai-sales-agent/
   ```

3. **Activate in WordPress**
   ```
   Plugins → Installed Plugins → Ai Sales Agent (ASA) → Activate
   ```

---

## 🔑 Step 2: Google Gemini API Setup

### Getting Your API Key

1. **Visit Google AI Studio**
   ```
   URL: https://aistudio.google.com/app/apikey
   ```

2. **Sign In**
   - Use your Google account credentials
   - Accept terms of service if prompted

3. **Create API Key**
   ```
   Click: "Create API Key" button
   Copy: The generated key (starts with "AIza...")
   ```

4. **Save Your Key**
   ⚠️ **Important**: Store your API key securely - you won't be able to view it again

### Understanding API Limits & Pricing

#### Free Tier Benefits
- **Generous Limits**: Millions of tokens per month
- **No Credit Card**: Required for free tier
- **Perfect for**: Small to medium websites

#### Paid Usage (After Free Tier)
- **Cost**: Typically pennies per thousand interactions
- **Billing**: Pay-as-you-use model
- **Monitoring**: Track usage in Google AI Studio

#### Rate Limits
- **Requests per minute**: 60 (free tier)
- **Tokens per minute**: 32,000 (free tier)
- **Automatic handling**: Plugin manages rate limiting gracefully

---

## ⚙️ Step 3: Plugin Configuration

### Accessing Settings

1. **Navigate to Settings**
   ```
   WordPress Admin → Settings → Ai Sales Agent (ASA)
   ```

2. **Settings Interface**
   - The settings page has three main tabs:
     - **General**: API key and system prompt
     - **Appearance**: Visual customization
     - **Behavior**: Functionality options

### General Settings Tab

#### API Key Configuration

1. **Enter API Key**
   ```
   Field: "Gemini API Key"
   Paste: Your Google Gemini API key
   ```

2. **Test Connection**
   ```
   Click: "Test API Key" button
   Wait: For confirmation message
   ```

3. **Expected Results**
   - ✅ **Success**: "Valid API Key!" message
   - ❌ **Error**: Check key format and network connection

#### System Prompt Customization

The system prompt defines your AI's personality and behavior:

**Default Prompt:**
```
You are ASA, a friendly and expert sales assistant for this website. 
Your primary goal is to be proactive, engaging, and helpful. 
Use the content of the page the user is viewing to understand their interests. 
Start conversations with insightful questions, highlight product benefits, 
answer questions clearly, and gently guide them towards making a purchase. 
Your tone should be persuasive but never pushy. 
Always aim to provide value and a great customer experience.
```

**Customization Examples:**

*For E-commerce:*
```
You are a knowledgeable product specialist for our online store. 
Help customers find the perfect products, answer technical questions, 
and provide detailed product comparisons. Focus on highlighting unique 
features and benefits that match customer needs.
```

*For Service Business:*
```
You are a professional consultant representing our services. 
Qualify leads by understanding their specific needs, explain our 
service offerings clearly, and guide qualified prospects toward 
scheduling a consultation or requesting a quote.
```

*For Educational Site:*
```
You are a helpful educational advisor. Assist students in finding 
relevant courses, answer questions about curriculum, and guide them 
through the enrollment process. Be encouraging and supportive.
```

### Appearance Settings Tab

#### Basic Branding

1. **Title Configuration**
   ```
   Field: "Title"
   Example: "Sales Assistant", "Help Desk", "AI Guide"
   Purpose: Appears in chat window header
   ```

2. **Subtitle Setup**
   ```
   Field: "Subtitle"
   Example: "How can I help you?", "Ask me anything!", "Let's chat!"
   Purpose: Secondary text in chat header
   ```

3. **Primary Color**
   ```
   Field: "Primary Color"
   Default: #333333
   Usage: Buttons, accents, launcher background
   Tip: Use your brand color for consistency
   ```

#### Avatar Configuration

**Option 1: FontAwesome Icons**
1. Click "Choose" button next to icon field
2. Select from 50+ available icons
3. Popular choices:
   - `fas fa-robot` (default)
   - `fas fa-user-astronaut`
   - `fas fa-headset`
   - `fas fa-comment-dots`

**Option 2: Custom Image**
1. Click "Upload Image" button
2. Select image from media library or upload new
3. Recommended specifications:
   - **Size**: 64x64px to 128x128px
   - **Format**: PNG with transparency preferred
   - **Style**: Simple, recognizable design

#### Position Settings

```
Options:
- Left: Chatbot appears on left side of screen
- Right: Chatbot appears on right side of screen (default)

Considerations:
- Right: More common, less intrusive
- Left: Better for RTL languages, unique positioning
```

### Behavior Settings Tab

#### Display Control

**Auto Insert Options:**
```
☑️ Enable Auto Insert: Automatically show chatbot on selected pages
☐ Disable: Use only shortcode/manual placement
```

**Page Type Selection:**
- **Everywhere**: Show on all pages (default)
- **Front Page**: Homepage only
- **Posts**: Individual blog posts
- **Pages**: Static pages
- **Archives**: Category, tag, date archives

**Custom Implementation:**
```php
// Use shortcode for specific placement
[asa_chatbot]

// Or in theme templates
<?php echo do_shortcode('[asa_chatbot]'); ?>
```

#### Proactive Messaging

**Delay Settings:**
```
Field: "Proactive Delay"
Default: 3000 (3 seconds)
Range: 1000-30000 (1-30 seconds)
Purpose: Time before showing contextual message
```

**How It Works:**
1. User visits page
2. Plugin analyzes page content
3. AI generates contextual conversation starter
4. Message appears after specified delay
5. User can dismiss or engage

**Examples of Proactive Messages:**
- Product page: "Interested in this product?"
- Service page: "Need help choosing a plan?"
- Blog post: "Questions about this topic?"

#### Chat History Management

```
Field: "History Limit"
Default: 50 messages
Range: 10-200 messages
Storage: User's browser localStorage
```

**Benefits:**
- Maintains conversation context
- Survives browser refreshes
- User privacy (local storage only)

#### Attribution Settings

```
Field: "Show Credit"
Options: Yes (default) / No
Display: "Developed by: Adem Isler" link
```

---

## 🎨 Step 4: Customization & Styling

### CSS Customization

Add custom styles to your theme's CSS or Customizer:

```css
/* Customize chatbot colors */
#asa-chatbot {
    --asa-color: #your-brand-color;
}

/* Custom launcher styling */
.asa-launcher {
    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
    border-radius: 50%;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

/* Chat window customization */
.asa-window {
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

/* Message bubble styling */
.asa-messages .user .bubble {
    background: #your-user-color;
}

.asa-messages .bot .bubble {
    background: #your-bot-color;
}
```

### Advanced Customization

**Hide on Specific Pages:**
```php
// Add to theme's functions.php
add_filter('asa_should_display', function($should_display) {
    if (is_page('privacy-policy') || is_page('terms')) {
        return false;
    }
    return $should_display;
});
```

**Custom System Prompts by Page:**
```php
add_filter('asa_system_prompt', function($prompt) {
    if (is_shop()) {
        return "You are a product specialist for our online store...";
    } elseif (is_page('contact')) {
        return "You are a helpful contact assistant...";
    }
    return $prompt;
});
```

---

## 🧪 Step 5: Testing & Verification

### Functionality Testing

#### 1. API Connection Test
```
Location: Settings → General Tab
Action: Click "Test API Key"
Expected: "Valid API Key!" message
```

#### 2. Frontend Display Test
```
Action: Visit your website frontend
Expected: Chatbot launcher visible in bottom corner
```

#### 3. Chat Functionality Test
```
Action: Click launcher → Type message → Send
Expected: AI response within 5-10 seconds
```

#### 4. Proactive Message Test
```
Action: Visit a content page → Wait 3 seconds
Expected: Contextual message bubble appears
```

#### 5. Mobile Responsiveness Test
```
Action: Test on mobile device
Expected: Full-screen chat interface
```

### Troubleshooting Common Issues

#### Chatbot Not Appearing
**Possible Causes:**
- Auto-insert disabled
- Page type not selected
- JavaScript errors
- Theme conflicts

**Solutions:**
1. Check Behavior settings
2. Verify page type selection
3. Check browser console for errors
4. Test with default theme

#### API Connection Failures
**Possible Causes:**
- Invalid API key
- Network restrictions
- Quota exceeded
- Server firewall

**Solutions:**
1. Verify API key in Google AI Studio
2. Check server outbound connections
3. Monitor API usage
4. Contact hosting provider

#### Proactive Messages Not Working
**Possible Causes:**
- Insufficient page content
- API quota issues
- JavaScript errors
- Caching conflicts

**Solutions:**
1. Ensure pages have substantial content
2. Check API usage limits
3. Clear caches
4. Test on different pages

---

## 📊 Step 6: Performance Optimization

### Caching Considerations

**Compatible Caching Plugins:**
- ✅ W3 Total Cache
- ✅ WP Rocket
- ✅ LiteSpeed Cache
- ✅ WP Super Cache

**Recommended Settings:**
```
- Exclude chatbot JavaScript from minification
- Don't cache AJAX endpoints
- Allow localStorage functionality
```

### CDN Configuration

**CloudFlare Settings:**
```
- Enable JavaScript minification: ✅
- Enable CSS minification: ✅
- Cache AJAX requests: ❌
- Respect cache headers: ✅
```

### Database Optimization

The plugin is designed for minimal database impact:
- **No chat history stored** in database
- **Minimal options** stored
- **Transient caching** for proactive messages
- **Clean uninstall** removes all data

---

## 🔒 Step 7: Security & Privacy

### Security Best Practices

1. **Keep Plugin Updated**
   ```
   Check: Plugins → Updates regularly
   Enable: Automatic updates if desired
   ```

2. **Secure API Key**
   ```
   Store: Only in WordPress admin
   Never: Share or expose publicly
   Rotate: Periodically for security
   ```

3. **Monitor Usage**
   ```
   Check: Google AI Studio dashboard
   Watch: For unusual activity
   Set: Usage alerts if available
   ```

### Privacy Compliance

#### GDPR Considerations
- **Data Processing**: Messages sent to Google Gemini API
- **Legal Basis**: Legitimate interest or consent
- **User Rights**: Inform users about AI processing
- **Data Retention**: No server-side storage

#### Recommended Privacy Notice
```
"Our website uses an AI-powered chatbot to assist visitors. 
When you use the chat feature, your messages and page content 
are processed by Google's Gemini AI service to provide responses. 
Chat history is stored locally in your browser and can be cleared 
at any time. For more information, see Google's privacy policy."
```

---

## 📈 Step 8: Analytics & Monitoring

### Google Analytics Integration

Add custom tracking to monitor chatbot performance:

```javascript
// Add to your theme's footer or analytics code
jQuery(document).ready(function($) {
    // Track chat opening
    $(document).on('click', '.asa-launcher', function() {
        gtag('event', 'chat_opened', {
            'event_category': 'engagement',
            'event_label': 'chatbot'
        });
    });
    
    // Track message sending
    $(document).on('click', '.asa-send', function() {
        gtag('event', 'message_sent', {
            'event_category': 'engagement',
            'event_label': 'chatbot'
        });
    });
});
```

### Performance Monitoring

**Key Metrics to Track:**
- Chat engagement rate
- Message volume
- API response times
- Conversion attribution
- User satisfaction

**Tools:**
- Google Analytics Events
- Hotjar or similar heatmap tools
- Google AI Studio usage dashboard
- WordPress performance plugins

---

## 🎓 Step 9: Advanced Configuration

### Multi-language Setup

1. **Install Translation Plugin**
   ```
   Recommended: WPML, Polylang, or Weglot
   ```

2. **Translate Plugin Strings**
   ```
   Use: Included .pot file for translations
   Translate: All user-facing text
   ```

3. **Multi-language Prompts**
   ```php
   add_filter('asa_system_prompt', function($prompt) {
       $lang = get_locale();
       switch($lang) {
           case 'es_ES':
               return "Eres un asistente de ventas amigable...";
           case 'fr_FR':
               return "Vous êtes un assistant commercial...";
           default:
               return $prompt;
       }
   });
   ```

### E-commerce Integration

**WooCommerce Specific Prompts:**
```php
add_filter('asa_system_prompt', function($prompt) {
    if (is_shop() || is_product_category() || is_product()) {
        return "You are a knowledgeable product specialist for our online store. 
                Help customers find products, compare options, and make informed 
                purchasing decisions. You can discuss features, benefits, pricing, 
                and availability. Guide customers toward adding items to their cart 
                when appropriate.";
    }
    return $prompt;
});
```

**Product-Specific Context:**
```php
add_filter('asa_system_prompt', function($prompt) {
    if (is_product()) {
        global $product;
        $product_info = "Current product: " . $product->get_name() . 
                       " - Price: " . $product->get_price_html();
        return $prompt . "\n\n" . $product_info;
    }
    return $prompt;
});
```

---

## ✅ Step 10: Launch Checklist

### Pre-Launch Verification

**Technical Checks:**
- [ ] Plugin activated and configured
- [ ] API key tested and working
- [ ] Chatbot appears on frontend
- [ ] Chat functionality working
- [ ] Proactive messages displaying
- [ ] Mobile responsiveness verified
- [ ] Cross-browser compatibility tested

**Content Checks:**
- [ ] System prompt customized for your business
- [ ] Title and subtitle set appropriately
- [ ] Brand colors configured
- [ ] Avatar selected or uploaded
- [ ] Display settings configured

**Performance Checks:**
- [ ] Page load times acceptable
- [ ] No JavaScript errors in console
- [ ] Caching compatibility verified
- [ ] CDN configuration tested

**Legal Checks:**
- [ ] Privacy policy updated
- [ ] Terms of service reviewed
- [ ] GDPR compliance considered
- [ ] User consent mechanism in place

### Post-Launch Monitoring

**Week 1:**
- Monitor API usage and costs
- Check for any error reports
- Gather initial user feedback
- Verify analytics tracking

**Month 1:**
- Analyze engagement metrics
- Review conversation quality
- Optimize system prompts
- Plan feature enhancements

**Ongoing:**
- Regular plugin updates
- API usage monitoring
- Performance optimization
- User experience improvements

---

## 🆘 Getting Help

### Support Resources

**Documentation:**
- Plugin settings page help text
- WordPress.org plugin page
- This installation guide

**Community Support:**
- [WordPress Support Forum](https://wordpress.org/support/plugin/asa-ai-sales-agent/)
- Plugin reviews and Q&A

**Professional Support:**
- Developer contact: [Adem Isler](https://ademisler.com/en)
- Custom development services
- Priority support options

### Reporting Issues

**Before Reporting:**
1. Check troubleshooting section
2. Test with default theme
3. Disable other plugins temporarily
4. Clear all caches

**What to Include:**
- WordPress version
- Plugin version
- PHP version
- Error messages (if any)
- Steps to reproduce issue
- Browser and device information

---

## 🎉 Congratulations!

You've successfully installed and configured Ai Sales Agent (ASA)! Your website now has an intelligent AI assistant that will:

- **Engage visitors proactively** with contextual conversation starters
- **Answer questions intelligently** using Google's advanced AI
- **Guide users through their journey** on your website
- **Increase conversions** through personalized interactions
- **Provide 24/7 support** without human intervention

### Next Steps

1. **Monitor Performance**: Keep an eye on engagement metrics and API usage
2. **Gather Feedback**: Ask users about their chat experience
3. **Optimize Prompts**: Refine your system prompt based on real conversations
4. **Expand Usage**: Consider adding the chatbot to more pages
5. **Stay Updated**: Keep the plugin updated for new features and improvements

**Ready to transform your website visitors into customers?** Your AI sales agent is now live and ready to help! 🚀

---

*For additional support or custom development needs, visit [ademisler.com](https://ademisler.com/en) or the [WordPress support forum](https://wordpress.org/support/plugin/asa-ai-sales-agent/).*