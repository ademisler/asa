# Ai Sales Agent (ASA) - Troubleshooting Guide 🔧

This comprehensive troubleshooting guide will help you diagnose and resolve common issues with Ai Sales Agent (ASA). Follow the step-by-step solutions to get your chatbot working perfectly.

## 🚨 Quick Diagnostic Checklist

Before diving into specific issues, run through this quick checklist:

- [ ] **Plugin Active**: Ai Sales Agent (ASA) is activated in WordPress
- [ ] **API Key Set**: Google Gemini API key is configured and tested
- [ ] **WordPress Version**: Running WordPress 5.0 or higher
- [ ] **PHP Version**: Server running PHP 7.4 or higher
- [ ] **JavaScript Enabled**: Browser has JavaScript enabled
- [ ] **Console Clean**: No JavaScript errors in browser console
- [ ] **Cache Cleared**: All caching plugins and browser cache cleared

---

## 🔍 Common Issues & Solutions

### 1. Chatbot Not Appearing on Frontend

#### Symptoms
- Chatbot launcher is not visible on website
- No chatbot elements in page source
- Settings configured but nothing shows

#### Possible Causes & Solutions

**🔧 Auto-Insert Disabled**
```
Problem: Auto-insert setting is turned off
Solution: 
1. Go to Settings → Ai Sales Agent (ASA) → Behavior Tab
2. Check "Enable Auto Insert"
3. Select appropriate page types
4. Save settings
```

**🔧 Wrong Page Types Selected**
```
Problem: Chatbot set to show only on specific page types
Solution:
1. Check current page type (post, page, archive, etc.)
2. Ensure matching page type is selected in settings
3. Or select "Everywhere" for testing
```

**🔧 Theme Conflicts**
```
Problem: Theme CSS or JavaScript conflicts
Solution:
1. Switch to default WordPress theme (Twenty Twenty-Three)
2. Test if chatbot appears
3. If yes, contact theme developer for compatibility
4. Add custom CSS to fix positioning if needed
```

**🔧 Plugin Conflicts**
```
Problem: Other plugins interfering
Solution:
1. Deactivate all other plugins
2. Test if chatbot appears
3. Reactivate plugins one by one to identify conflict
4. Contact conflicting plugin developer
```

**🔧 Caching Issues**
```
Problem: Cached pages not showing updated content
Solution:
1. Clear all caching plugins
2. Clear browser cache (Ctrl+F5)
3. Clear CDN cache if using one
4. Exclude chatbot files from caching
```

### 2. API Connection Problems

#### Symptoms
- "Test API Key" shows error
- Chat messages don't get responses
- Error messages in browser console
- "Configure API key in settings" placeholder

#### Possible Causes & Solutions

**🔧 Invalid API Key**
```
Problem: API key is incorrect or expired
Solution:
1. Visit https://aistudio.google.com/app/apikey
2. Verify your API key is active
3. Generate new key if needed
4. Copy exact key (starts with "AIza...")
5. Paste in plugin settings
6. Test connection
```

**🔧 API Key Format Issues**
```
Problem: Extra spaces or characters in API key
Solution:
1. Ensure no leading/trailing spaces
2. Key should be exactly as generated
3. No line breaks or special characters
4. Re-copy from Google AI Studio if unsure
```

**🔧 Network/Firewall Restrictions**
```
Problem: Server cannot reach Google API
Solution:
1. Check server outbound connections
2. Whitelist generativelanguage.googleapis.com
3. Ensure port 443 (HTTPS) is open
4. Contact hosting provider if blocked
5. Test from different network/device
```

**🔧 API Quota Exceeded**
```
Problem: Google API usage limits reached
Solution:
1. Check usage in Google AI Studio dashboard
2. Wait for quota reset (usually monthly)
3. Upgrade to paid tier if needed
4. Monitor usage patterns
5. Implement usage controls if necessary
```

**🔧 Geographic Restrictions**
```
Problem: Google Gemini not available in your region
Solution:
1. Check Google AI availability by country
2. Use VPN for testing (not production)
3. Consider alternative hosting location
4. Contact Google for regional availability
```

### 3. Proactive Messages Not Working

#### Symptoms
- No proactive message bubbles appear
- Messages appear but with generic content
- Proactive messages show on wrong pages
- Timing issues with message display

#### Possible Causes & Solutions

**🔧 Insufficient Page Content**
```
Problem: Page doesn't have enough content for AI analysis
Solution:
1. Ensure pages have substantial text content
2. Check content is in standard HTML elements
3. Test on content-rich pages first
4. Add more descriptive content if needed
```

**🔧 Content Extraction Issues**
```
Problem: Plugin can't find page content
Solution:
1. Check page uses standard content containers
2. Look for .entry-content, .post-content, article, main
3. Add custom content selector if needed
4. Test with different themes
```

**🔧 Proactive Messages Dismissed**
```
Problem: User previously dismissed proactive messages
Solution:
1. Clear browser localStorage
2. Use incognito/private browsing mode
3. Test from different browser/device
4. Check sessionStorage for 'asa_proactive_closed'
```

**🔧 Timing Configuration**
```
Problem: Delay too short or too long
Solution:
1. Adjust "Proactive Delay" in settings
2. Recommended range: 3000-10000ms (3-10 seconds)
3. Test different values for optimal timing
4. Consider user behavior patterns
```

**🔧 JavaScript Errors**
```
Problem: JavaScript errors preventing execution
Solution:
1. Check browser console for errors
2. Look for conflicts with other scripts
3. Test with minimal plugins active
4. Update to latest plugin version
```

### 4. Chat Functionality Issues

#### Symptoms
- Messages sent but no responses
- Error messages in chat window
- Chat window opens but input disabled
- Typing indicator stuck

#### Possible Causes & Solutions

**🔧 AJAX Request Failures**
```
Problem: WordPress AJAX not working
Solution:
1. Check admin-ajax.php is accessible
2. Verify WordPress AJAX configuration
3. Check for permalink issues
4. Test with default permalinks
5. Ensure wp_nonce working correctly
```

**🔧 Input Sanitization Issues**
```
Problem: Messages blocked by security plugins
Solution:
1. Check security plugin logs
2. Whitelist ASA AJAX actions
3. Adjust security plugin settings
4. Test with security plugins disabled
```

**🔧 Server Response Issues**
```
Problem: Server not responding to requests
Solution:
1. Check server error logs
2. Increase PHP memory limit if needed
3. Check for server timeout issues
4. Verify server can make external requests
```

**🔧 Browser Compatibility**
```
Problem: Chatbot not working in specific browsers
Solution:
1. Test in multiple browsers
2. Check for browser-specific JavaScript errors
3. Update browser to latest version
4. Check for browser extensions blocking functionality
```

### 5. Mobile Experience Problems

#### Symptoms
- Chatbot not responsive on mobile
- Chat window too small or large
- Touch interactions not working
- Keyboard issues on mobile

#### Possible Causes & Solutions

**🔧 Viewport Configuration**
```
Problem: Mobile viewport not configured
Solution:
1. Ensure theme has proper viewport meta tag
2. Add: <meta name="viewport" content="width=device-width, initial-scale=1">
3. Test responsive design
4. Check for CSS conflicts
```

**🔧 Touch Event Issues**
```
Problem: Touch events not registering
Solution:
1. Check for CSS pointer-events conflicts
2. Ensure touch targets are large enough (44px minimum)
3. Test on actual devices, not just browser tools
4. Check for JavaScript touch event handlers
```

**🔧 Mobile CSS Issues**
```
Problem: Styling broken on mobile
Solution:
1. Check responsive CSS rules
2. Test at different screen sizes
3. Adjust mobile-specific styles
4. Use browser dev tools mobile simulation
```

### 6. Performance Issues

#### Symptoms
- Slow page loading
- Chatbot appears with delay
- High server resource usage
- Slow API responses

#### Possible Causes & Solutions

**🔧 Asset Loading Issues**
```
Problem: Large assets slowing page load
Solution:
1. Enable asset minification
2. Use CDN for static assets
3. Optimize image assets
4. Check for unused CSS/JS
```

**🔧 API Response Times**
```
Problem: Google API responding slowly
Solution:
1. Check Google API status page
2. Test from different geographic locations
3. Monitor API response times
4. Consider caching strategies for repeated requests
```

**🔧 Database Performance**
```
Problem: WordPress database queries slow
Solution:
1. Optimize WordPress database
2. Check for slow queries
3. Enable object caching
4. Monitor database performance
```

---

## 🛠️ Advanced Debugging Techniques

### Browser Console Debugging

**Enable Console Logging:**
```javascript
// Add to browser console for detailed logging
localStorage.setItem('asa_debug', 'true');
// Reload page to see debug messages
```

**Common Console Errors:**
```javascript
// AJAX errors
"Failed to load resource: the server responded with a status of 403"
Solution: Check nonce verification and user permissions

// JavaScript errors
"Uncaught TypeError: Cannot read property of undefined"
Solution: Check for missing dependencies or conflicts

// Network errors
"net::ERR_BLOCKED_BY_CLIENT"
Solution: Check for ad blockers or browser extensions
```

### WordPress Debug Mode

**Enable WordPress Debugging:**
```php
// Add to wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Check logs at: /wp-content/debug.log
```

**Common Log Entries:**
```
PHP Fatal error: Allowed memory size exhausted
Solution: Increase PHP memory limit

Warning: Cannot modify header information
Solution: Check for output before headers

Notice: Undefined index
Solution: Update plugin to latest version
```

### Server-Side Debugging

**Check Server Logs:**
```bash
# Apache error log
tail -f /var/log/apache2/error.log

# Nginx error log
tail -f /var/log/nginx/error.log

# PHP error log
tail -f /var/log/php_errors.log
```

**Common Server Issues:**
```
403 Forbidden errors: Check file permissions
500 Internal Server Error: Check PHP errors
502 Bad Gateway: Check server configuration
504 Gateway Timeout: Increase timeout limits
```

### Network Debugging

**Test API Connectivity:**
```bash
# Test Google API endpoint
curl -X POST \
  'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=YOUR_API_KEY' \
  -H 'Content-Type: application/json' \
  -d '{"contents":[{"role":"user","parts":[{"text":"Hello"}]}]}'
```

**Check DNS Resolution:**
```bash
# Verify DNS resolution
nslookup generativelanguage.googleapis.com

# Test connectivity
ping generativelanguage.googleapis.com
```

---

## 🔧 Environment-Specific Issues

### Shared Hosting Issues

**Common Problems:**
- Limited outbound connections
- Restricted file permissions
- PHP function limitations
- Resource constraints

**Solutions:**
```
1. Contact hosting provider about API access
2. Check for curl/file_get_contents restrictions
3. Verify wp_remote_post functionality
4. Consider upgrading hosting plan
```

### WordPress Multisite Issues

**Common Problems:**
- Network-wide activation issues
- Site-specific configurations
- Asset loading problems

**Solutions:**
```
1. Activate plugin per-site, not network-wide
2. Check multisite-specific WordPress functions
3. Verify asset URLs are correct
4. Test on main site first
```

### CDN and Caching Issues

**CloudFlare Specific:**
```
Problem: AJAX requests cached incorrectly
Solution:
1. Add page rule for /wp-admin/admin-ajax.php
2. Set cache level to "Bypass"
3. Disable "Always Online" for admin
```

**WP Rocket Specific:**
```
Problem: JavaScript not loading
Solution:
1. Exclude asa-script.js from minification
2. Add AJAX URL to cache exclusions
3. Clear all caches after changes
```

---

## 📱 Mobile-Specific Troubleshooting

### iOS Safari Issues

**Common Problems:**
- Touch events not working
- Viewport scaling issues
- localStorage limitations

**Solutions:**
```css
/* Fix touch events */
.asa-launcher {
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    touch-action: manipulation;
}

/* Fix viewport issues */
.asa-window {
    -webkit-overflow-scrolling: touch;
}
```

### Android Chrome Issues

**Common Problems:**
- Keyboard covering input
- Touch target size issues
- Performance on older devices

**Solutions:**
```javascript
// Handle keyboard covering input
window.addEventListener('resize', function() {
    if (document.activeElement.classList.contains('asa-text')) {
        document.activeElement.scrollIntoView();
    }
});
```

---

## 🔒 Security-Related Issues

### Content Security Policy (CSP)

**Problem:** CSP blocking external requests
```html
<!-- Add to CSP header -->
connect-src 'self' https://generativelanguage.googleapis.com;
font-src 'self' https://fonts.gstatic.com;
style-src 'self' https://fonts.googleapis.com;
```

### CORS Issues

**Problem:** Cross-origin requests blocked
```
Note: Google Gemini API should not have CORS issues
as requests are made server-side, not client-side.
If seeing CORS errors, check for theme/plugin conflicts.
```

### Firewall Issues

**Problem:** WAF blocking API requests
```
1. Check firewall logs for blocked requests
2. Whitelist Google API endpoints
3. Adjust security rules if needed
4. Contact hosting provider for assistance
```

---

## 🧪 Testing Procedures

### Systematic Testing Approach

**1. Basic Functionality Test:**
```
□ Plugin activates without errors
□ Settings page loads correctly
□ API key test passes
□ Chatbot appears on frontend
□ Chat messages work
□ Proactive messages appear
```

**2. Cross-Browser Testing:**
```
□ Chrome (latest)
□ Firefox (latest)
□ Safari (latest)
□ Edge (latest)
□ Mobile browsers
```

**3. Performance Testing:**
```
□ Page load times acceptable
□ No JavaScript errors
□ API response times reasonable
□ Mobile performance good
```

### Automated Testing

**Browser Console Tests:**
```javascript
// Test chatbot initialization
console.log('ASA Chatbot:', $('#asa-chatbot').length > 0);

// Test settings availability
console.log('ASA Settings:', typeof asaSettings !== 'undefined');

// Test API key status
console.log('Has API Key:', asaSettings.hasApiKey);
```

---

## 📞 Getting Help

### Before Contacting Support

**Gather Information:**
- WordPress version
- Plugin version
- PHP version
- Server type (Apache/Nginx)
- Hosting provider
- Active plugins list
- Theme name and version
- Browser and device used
- Exact error messages
- Steps to reproduce issue

### Support Channels

**Community Support:**
- [WordPress Support Forum](https://wordpress.org/support/plugin/asa-ai-sales-agent/)
- Plugin reviews and Q&A section

**Documentation:**
- Plugin settings help text
- README.md file
- Installation guide

**Professional Support:**
- Developer: [Adem Isler](https://ademisler.com/en)
- Custom development services
- Priority support options

### Creating Effective Bug Reports

**Include These Details:**
```
1. Clear description of the problem
2. Steps to reproduce the issue
3. Expected vs actual behavior
4. Screenshots or screen recordings
5. Browser console errors
6. WordPress/plugin/theme versions
7. List of active plugins
8. Server environment details
```

---

## 🚀 Performance Optimization Tips

### General Optimization

**1. Minimize HTTP Requests:**
```
- Combine CSS/JS files where possible
- Use CSS sprites for icons
- Optimize images and assets
```

**2. Enable Caching:**
```
- Use WordPress caching plugins
- Enable browser caching
- Consider CDN for static assets
```

**3. Optimize Database:**
```
- Clean up unused data
- Optimize database tables
- Use object caching
```

### Plugin-Specific Optimization

**1. Proactive Message Caching:**
```
- Messages cached for 1 hour by default
- Reduces API calls for repeated visits
- Cache key based on page content
```

**2. Asset Loading:**
```
- CSS/JS loaded only when needed
- Dependencies properly managed
- Version numbers for cache busting
```

**3. API Usage Optimization:**
```
- Efficient payload structure
- Proper error handling
- Rate limiting respect
```

---

## 📊 Monitoring and Maintenance

### Regular Maintenance Tasks

**Weekly:**
- Check for plugin updates
- Monitor API usage
- Review error logs
- Test basic functionality

**Monthly:**
- Analyze performance metrics
- Review user feedback
- Update documentation
- Plan improvements

**Quarterly:**
- Full functionality testing
- Security review
- Performance optimization
- Feature planning

### Monitoring Tools

**WordPress-Specific:**
- Query Monitor plugin
- Debug Bar plugin
- WordPress.com monitoring

**General Tools:**
- Google Analytics
- Google Search Console
- Uptime monitoring services
- Performance monitoring tools

---

## 🎯 Prevention Best Practices

### Avoid Common Issues

**1. Regular Updates:**
```
- Keep WordPress core updated
- Update plugins regularly
- Update themes when needed
- Monitor security advisories
```

**2. Proper Testing:**
```
- Test updates on staging site first
- Use version control
- Backup before major changes
- Document configuration changes
```

**3. Monitor Performance:**
```
- Regular performance audits
- Monitor API usage patterns
- Track user engagement
- Watch for error patterns
```

### Development Best Practices

**1. Code Quality:**
```
- Follow WordPress coding standards
- Use proper error handling
- Implement security best practices
- Document code changes
```

**2. Testing:**
```
- Test across different environments
- Use automated testing where possible
- Validate user input properly
- Test edge cases
```

---

This troubleshooting guide should help you resolve most issues with Ai Sales Agent (ASA). If you encounter problems not covered here, don't hesitate to reach out for support through the WordPress.org support forum or contact the developer directly.

Remember: Most issues can be resolved by following the systematic approach outlined above. Start with the basics and work your way through to more advanced solutions. 🔧✨