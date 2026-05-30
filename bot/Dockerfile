FROM python:3.9-slim-buster
WORKDIR /app
COPY ./bot /app
RUN pip install --no-cache-dir -r requirements.txt
ENV TELEGRAM_BOT_TOKEN="8146069383:AAGaiNYOM22xQVp2PJ6Zv9nZnbo1EThjlSY"
ENV ADMIN_USER_ID="7059256157"
CMD ["python", "bot_updated.py"]
