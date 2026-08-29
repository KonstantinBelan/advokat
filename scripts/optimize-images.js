const fs = require('fs');
const path = require('path');
const sharp = require('sharp');
const chokidar = require('chokidar');

// Paths
const SRC_DIR = path.join(__dirname, '..', 'src', 'images');
const DIST_DIR = path.join(__dirname, '..', 'assets', 'img');

// Configuration
const CONFIG = {
  jpeg: { quality: 92, mozjpeg: true, progressive: true },
  png: { compressionLevel: 9, palette: true, quality: 92 },
  webp: { quality: 92, effort: 6 },
  avif: { quality: 92, effort: 6 },
};

// Size configurations based on file name or generic fallback
function getImageSizes(filename, meta) {
  const name = path.parse(filename).name;
  const originalWidth = meta.width;

  // Specific preset mappings
  if (name.startsWith('hero')) {
    return [
      { suffix: '-mobile', width: 480 },
      { suffix: '-tablet', width: 768 },
      { suffix: '-desktop', width: 1200 },
      { suffix: '-2x', width: Math.min(2400, originalWidth) },
      { suffix: '', width: 1200 },
    ];
  }

  if (name.startsWith('expertise')) {
    return [
      { suffix: '-sm', width: 360 },
      { suffix: '', width: 575 },
      { suffix: '-2x', width: Math.min(1150, originalWidth) },
    ];
  }

  if (name.startsWith('about')) {
    return [
      { suffix: '-sm', width: 360 },
      { suffix: '', width: 476 },
      { suffix: '-2x', width: Math.min(952, originalWidth) },
    ];
  }

  if (name.startsWith('article')) {
    return [
      { suffix: '-sm', width: 360 },
      { suffix: '', width: 364 },
      { suffix: '-2x', width: Math.min(728, originalWidth) },
    ];
  }

  // Generic fallback: standard, 2x, and sm
  if (originalWidth > 800) {
    return [
      { suffix: '-sm', width: 480 },
      { suffix: '', width: Math.round(originalWidth / 2) },
      { suffix: '-2x', width: originalWidth },
    ];
  }

  return [
    { suffix: '-sm', width: Math.max(320, Math.round(originalWidth * 0.6)) },
    { suffix: '', width: originalWidth },
  ];
}

// Format bytes helper
function formatBytes(bytes) {
  if (bytes === 0) return '0 B';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return (bytes / Math.pow(k, i)).toFixed(1) + ' ' + sizes[i];
}

// Process a single image file
async function processImage(file, force = false) {
  const srcPath = path.join(SRC_DIR, file);
  if (!fs.existsSync(srcPath)) return;

  const stat = fs.statSync(srcPath);
  if (stat.isDirectory()) return;

  const ext = path.extname(file).toLowerCase();
  const baseName = path.parse(file).name;

  if (!['.jpg', '.jpeg', '.png', '.webp', '.avif'].includes(ext)) {
    // Copy SVGs or non-raster files directly
    const distPath = path.join(DIST_DIR, file);
    fs.copyFileSync(srcPath, distPath);
    console.log(`📋 Copied: ${file}`);
    return;
  }

  try {
    const image = sharp(srcPath);
    const meta = await image.metadata();
    const sizes = getImageSizes(file, meta);

    console.log(`\n🖼️  Processing: ${file} (${meta.width}x${meta.height}, ${formatBytes(stat.size)})`);

    let totalSaved = 0;
    let count = 0;

    for (const sizeConfig of sizes) {
      const targetWidth = Math.min(sizeConfig.width, meta.width);
      const outputBaseName = `${baseName}${sizeConfig.suffix}`;

      // Formats to generate: AVIF, WebP, and original format
      const tasks = [
        {
          ext: '.avif',
          process: (pipeline) => pipeline.avif(CONFIG.avif),
        },
        {
          ext: '.webp',
          process: (pipeline) => pipeline.webp(CONFIG.webp),
        },
        {
          ext: ext === '.png' ? '.png' : '.jpg',
          process: (pipeline) =>
            ext === '.png' ? pipeline.png(CONFIG.png) : pipeline.jpeg(CONFIG.jpeg),
        },
      ];

      for (const task of tasks) {
        const outFileName = `${outputBaseName}${task.ext}`;
        const outFilePath = path.join(DIST_DIR, outFileName);

        // Check if output is already up-to-date
        if (!force && fs.existsSync(outFilePath)) {
          const outStat = fs.statSync(outFilePath);
          if (outStat.mtime >= stat.mtime) {
            continue;
          }
        }

        const pipeline = sharp(srcPath).resize({
          width: targetWidth,
          withoutEnlargement: true,
        });

        await task.process(pipeline).toFile(outFilePath);

        const outStat = fs.statSync(outFilePath);
        count++;
        console.log(`   ✓ Generated ${outFileName.padEnd(28)} [${targetWidth}px] ${formatBytes(outStat.size)}`);
      }
    }

    if (count === 0) {
      console.log(`   ⚡ Already up to date.`);
    }
  } catch (err) {
    console.error(`❌ Error processing ${file}:`, err.message);
  }
}

// Process all images in SRC_DIR
async function processAllImages(force = false) {
  if (!fs.existsSync(SRC_DIR)) {
    fs.mkdirSync(SRC_DIR, { recursive: true });
  }
  if (!fs.existsSync(DIST_DIR)) {
    fs.mkdirSync(DIST_DIR, { recursive: true });
  }

  const files = fs.readdirSync(SRC_DIR);
  const imageFiles = files.filter((f) =>
    ['.jpg', '.jpeg', '.png', '.webp', '.avif', '.svg'].includes(path.extname(f).toLowerCase())
  );

  if (imageFiles.length === 0) {
    console.log(`ℹ️  No images found in ${SRC_DIR}. Add images there to optimize.`);
    return;
  }

  console.log(`🚀 Optimizing ${imageFiles.length} image(s) from ${SRC_DIR} -> ${DIST_DIR}...`);
  for (const file of imageFiles) {
    await processImage(file, force);
  }
  console.log(`\n✨ All images optimized successfully!\n`);
}

// Watch mode
function startWatchMode() {
  console.log(`👀 Watching ${SRC_DIR} for image changes...`);

  const watcher = chokidar.watch(SRC_DIR, {
    ignored: /(^|[\/\\])\../,
    persistent: true,
    ignoreInitial: true,
  });

  watcher
    .on('add', (filePath) => {
      const file = path.basename(filePath);
      console.log(`➕ Added: ${file}`);
      processImage(file, true);
    })
    .on('change', (filePath) => {
      const file = path.basename(filePath);
      console.log(`🔄 Changed: ${file}`);
      processImage(file, true);
    })
    .on('unlink', (filePath) => {
      const file = path.basename(filePath);
      const baseName = path.parse(file).name;
      console.log(`🗑️  Deleted source: ${file}`);
      // Remove generated files
      if (fs.existsSync(DIST_DIR)) {
        const distFiles = fs.readdirSync(DIST_DIR);
        distFiles
          .filter((f) => f.startsWith(baseName))
          .forEach((f) => {
            fs.unlinkSync(path.join(DIST_DIR, f));
            console.log(`   Removed generated: ${f}`);
          });
      }
    });
}

// Entry
const isWatch = process.argv.includes('--watch') || process.argv.includes('-w');
const isForce = process.argv.includes('--force') || process.argv.includes('-f');

(async () => {
  await processAllImages(isForce);
  if (isWatch) {
    startWatchMode();
  }
})();
