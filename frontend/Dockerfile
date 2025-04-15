FROM node:18
WORKDIR /app

# 1. First copy package files (handles missing package-lock.json)
COPY package.json ./
# Conditional copy for package-lock.json if it exists
RUN if [ -f ../package-lock.json ]; then \
      cp ../package-lock.json ./; \
    fi

# 2. Install dependencies
RUN if [ -f package-lock.json ]; then \
      npm ci --ignore-scripts; \
    else \
      npm install --ignore-scripts; \
    fi

# 3. Install Quasar CLI
RUN npm install -g @quasar/cli@latest

# 4. Copy all remaining files
COPY . .

# 5. Prepare Quasar with ES modules support
RUN NODE_OPTIONS="--no-warnings --loader ts-node/esm" npx quasar prepare || \
    (echo "Quasar prepare may have failed - continuing build..." && exit 0)

# 6. Final dependency check
RUN npm install

CMD ["quasar", "dev", "--hostname", "0.0.0.0", "--port", "3000"]