// Universal Web API polyfills for Next.js in Phusion Passenger
try {
  const primitives = require("next/dist/compiled/@edge-runtime/primitives");
  for (const [key, value] of Object.entries(primitives)) {
    if (typeof globalThis[key] === "undefined") {
      globalThis[key] = value;
    }
    if (typeof global[key] === "undefined") {
      global[key] = value;
    }
  }
} catch (e) {
  console.error("Failed to load edge primitives:", e);
}

const http = require("http");
const { parse } = require("url");
const next = require("next");
const fs = require("fs");
const path = require("path");

const dev = false;
const port = process.env.PORT || 3000;
const appDir = __dirname;

const logFile = path.join(appDir, "server_error.log");
function logError(msg) {
  try {
    fs.appendFileSync(logFile, `[${new Date().toISOString()}] ${msg}\n`);
  } catch (e) {}
}

logError(`\n=== Server starting with Node ${process.version} at ${appDir} ===`);

const app = next({ dev, dir: appDir });
const handle = app.getRequestHandler();

let prepareError = null;
let isPrepared = false;

const preparePromise = app.prepare()
  .then(() => {
    isPrepared = true;
    logError("Next.js app prepared successfully! Ready to serve requests.");
  })
  .catch((err) => {
    prepareError = err;
    logError("Next.js prepare failed: " + (err.stack || err.message));
  });

const server = http.createServer(async (req, res) => {
  try {
    if (prepareError) {
      logError("Handling request while prepare failed: " + prepareError.message);
      res.statusCode = 500;
      res.setHeader("Content-Type", "text/plain");
      return res.end("Server Build Initialization Error: " + prepareError.message);
    }

    if (!isPrepared) {
      await preparePromise;
      if (prepareError) {
        res.statusCode = 500;
        res.setHeader("Content-Type", "text/plain");
        return res.end("Server Build Initialization Error: " + prepareError.message);
      }
    }

    const parsedUrl = parse(req.url, true);
    await handle(req, res, parsedUrl);
  } catch (err) {
    logError("Request Handler Error: " + (err.stack || err.message));
    res.statusCode = 500;
    res.setHeader("Content-Type", "text/plain");
    res.end("Internal Server Error: " + (err.message || "Unknown error"));
  }
});

if (typeof PhusionPassenger !== "undefined") {
  server.listen("passenger");
} else {
  server.listen(port, () => {
    logError(`Next.js listening on port ${port}`);
  });
}
