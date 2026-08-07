// api/config.js

export default function handler(req, res) {
  // Allow cross-origin requests
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET');

  // Reject any request method other than GET
  if (req.method !== 'GET') {
    return res.status(405).json({ error: 'Method Not Allowed' });
  }

  try {
    // Verify required environment variables exist in Vercel
    const requiredEnvVars = [
      'SUPABASE_URL',
      'SUPABASE_ANON_KEY',
      'FIREBASE_API_KEY',
      'FIREBASE_AUTH_DOMAIN',
      'FIREBASE_PROJECT_ID'
    ];

    const missingVars = requiredEnvVars.filter((key) => !process.env[key]);

    if (missingVars.length > 0) {
      console.error('Missing Environment Variables in Vercel:', missingVars);
      return res.status(500).json({
        error: 'Missing backend environment variables on Vercel.',
        missingVariables: missingVars
      });
    }

    // Return clean client configuration payload
    return res.status(200).json({
      SUPABASE_URL: process.env.SUPABASE_URL,
      SUPABASE_ANON_KEY: process.env.SUPABASE_ANON_KEY,
      firebaseConfig: {
        apiKey: process.env.FIREBASE_API_KEY,
        authDomain: process.env.FIREBASE_AUTH_DOMAIN,
        projectId: process.env.FIREBASE_PROJECT_ID,
        storageBucket: process.env.FIREBASE_STORAGE_BUCKET || "",
        messagingSenderId: process.env.FIREBASE_MESSAGING_SENDER_ID || "",
        appId: process.env.FIREBASE_APP_ID || ""
      }
    });

  } catch (error) {
    console.error('Internal Error in api/config.js:', error);
    return res.status(500).json({
      error: 'Internal Server Error',
      details: error.message
    });
  }
}
