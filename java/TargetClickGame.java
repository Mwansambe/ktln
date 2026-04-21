import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.Random;

/**
 * Target Click Game - A simple reaction-based game with a clean UI.
 * Click the moving circle to score points within the time limit.
 */
public class TargetClickGame extends JFrame {
    // Game constants
    private static final int GAME_DURATION = 30;        // seconds
    private static final int TARGET_SIZE = 70;          // diameter in pixels
    private static final int AUTO_MOVE_INTERVAL = 2000; // milliseconds

    // Game state
    private int score = 0;
    private int timeLeft = GAME_DURATION;
    private boolean gameActive = false;
    private int targetX = 0;
    private int targetY = 0;

    // UI Components
    private GamePanel gamePanel;
    private JLabel scoreLabel;
    private JLabel timerLabel;
    private JButton restartButton;

    // Timers
    private Timer countdownTimer;
    private Timer moverTimer;
    private Random random = new Random();

    /**
     * Constructor - Sets up the game window and UI.
     */
    public TargetClickGame() {
        setTitle("Target Click Game");
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setResizable(false);
        initUI();
        pack();
        setLocationRelativeTo(null);
        resetGame(); // Initialize game state
    }

    /**
     * Initializes all UI components and layout.
     */
    private void initUI() {
        // Create game panel (where the target is drawn)
        gamePanel = new GamePanel();
        gamePanel.setPreferredSize(new Dimension(600, 400));
        gamePanel.setBackground(new Color(245, 245, 250));
        gamePanel.setBorder(BorderFactory.createLineBorder(new Color(200, 200, 210), 2));
        
        // Add mouse listener to game panel for clicking the target
        gamePanel.addMouseListener(new MouseAdapter() {
            @Override
            public void mouseClicked(MouseEvent e) {
                if (gameActive && isPointInTarget(e.getPoint())) {
                    incrementScore();
                }
            }
        });

        // Create top panel for score, timer, and restart button
        JPanel topPanel = new JPanel(new FlowLayout(FlowLayout.CENTER, 20, 10));
        topPanel.setBackground(new Color(50, 50, 70));
        topPanel.setBorder(BorderFactory.createEmptyBorder(10, 10, 10, 10));

        // Score label
        scoreLabel = new JLabel("Score: 0");
        scoreLabel.setFont(new Font("Segoe UI", Font.BOLD, 20));
        scoreLabel.setForeground(Color.WHITE);

        // Timer label
        timerLabel = new JLabel("Time: " + GAME_DURATION + "s");
        timerLabel.setFont(new Font("Segoe UI", Font.BOLD, 20));
        timerLabel.setForeground(Color.WHITE);

        // Restart button
        restartButton = new JButton("New Game");
        restartButton.setFont(new Font("Segoe UI", Font.BOLD, 14));
        restartButton.setBackground(new Color(100, 150, 200));
        restartButton.setForeground(Color.WHITE);
        restartButton.setFocusPainted(false);
        restartButton.setBorder(BorderFactory.createEmptyBorder(8, 20, 8, 20));
        restartButton.addActionListener(e -> resetGame());

        topPanel.add(scoreLabel);
        topPanel.add(timerLabel);
        topPanel.add(restartButton);

        // Instruction label at bottom
        JLabel instructionLabel = new JLabel("Click the circle to score! It moves every 2 seconds.", SwingConstants.CENTER);
        instructionLabel.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        instructionLabel.setForeground(new Color(80, 80, 100));
        instructionLabel.setBorder(BorderFactory.createEmptyBorder(8, 0, 8, 0));

        // Add components to frame
        setLayout(new BorderLayout());
        add(topPanel, BorderLayout.NORTH);
        add(gamePanel, BorderLayout.CENTER);
        add(instructionLabel, BorderLayout.SOUTH);
    }

    /**
     * Checks if a given point is inside the target circle.
     */
    private boolean isPointInTarget(Point p) {
        int centerX = targetX + TARGET_SIZE / 2;
        int centerY = targetY + TARGET_SIZE / 2;
        int radius = TARGET_SIZE / 2;
        int dx = p.x - centerX;
        int dy = p.y - centerY;
        return (dx * dx + dy * dy) <= (radius * radius);
    }

    /**
     * Increments the player's score and moves the target.
     */
    private void incrementScore() {
        if (!gameActive) return;
        score++;
        updateScoreLabel();
        repositionTarget();
        // Optional: flash effect could be added here
    }

    /**
     * Updates the score display.
     */
    private void updateScoreLabel() {
        scoreLabel.setText("Score: " + score);
    }

    /**
     * Updates the timer display.
     */
    private void updateTimerLabel() {
        timerLabel.setText("Time: " + timeLeft + "s");
    }

    /**
     * Moves the target to a random position within the game panel.
     */
    private void repositionTarget() {
        int panelWidth = gamePanel.getWidth();
        int panelHeight = gamePanel.getHeight();
        
        // Ensure panel dimensions are valid
        if (panelWidth <= TARGET_SIZE || panelHeight <= TARGET_SIZE) {
            return;
        }
        
        int maxX = panelWidth - TARGET_SIZE;
        int maxY = panelHeight - TARGET_SIZE;
        targetX = random.nextInt(maxX + 1);
        targetY = random.nextInt(maxY + 1);
        gamePanel.repaint();
    }

    /**
     * Ends the game, disables scoring, and stops timers.
     */
    private void endGame() {
        gameActive = false;
        if (countdownTimer != null) countdownTimer.stop();
        if (moverTimer != null) moverTimer.stop();
        gamePanel.repaint(); // Show game over overlay
    }

    /**
     * Resets and starts a new game.
     */
    private void resetGame() {
        // Stop any running timers
        if (countdownTimer != null) countdownTimer.stop();
        if (moverTimer != null) moverTimer.stop();
        
        // Reset game state
        gameActive = true;
        score = 0;
        timeLeft = GAME_DURATION;
        updateScoreLabel();
        updateTimerLabel();
        
        // Set initial target position
        repositionTarget();
        
        // Start countdown timer
        countdownTimer = new Timer(1000, e -> {
            if (gameActive) {
                timeLeft--;
                updateTimerLabel();
                if (timeLeft <= 0) {
                    endGame();
                }
            }
        });
        countdownTimer.start();
        
        // Start auto-move timer (moves target every few seconds)
        moverTimer = new Timer(AUTO_MOVE_INTERVAL, e -> {
            if (gameActive) {
                repositionTarget();
            }
        });
        moverTimer.start();
        
        gamePanel.repaint();
    }

    /**
     * Inner class - The custom JPanel that draws the game graphics.
     */
    private class GamePanel extends JPanel {
        @Override
        protected void paintComponent(Graphics g) {
            super.paintComponent(g);
            Graphics2D g2d = (Graphics2D) g;
            g2d.setRenderingHint(RenderingHints.KEY_ANTIALIASING, RenderingHints.VALUE_ANTIALIAS_ON);
            
            // Draw background
            g2d.setColor(getBackground());
            g2d.fillRect(0, 0, getWidth(), getHeight());
            
            // Draw target circle with gradient and shadow
            if (targetX >= 0 && targetY >= 0 && targetX + TARGET_SIZE <= getWidth() && targetY + TARGET_SIZE <= getHeight()) {
                // Shadow
                g2d.setColor(new Color(0, 0, 0, 50));
                g2d.fillOval(targetX + 5, targetY + 5, TARGET_SIZE, TARGET_SIZE);
                
                // Main circle gradient
                GradientPaint gradient = new GradientPaint(
                    targetX, targetY, new Color(255, 100, 100),
                    targetX + TARGET_SIZE, targetY + TARGET_SIZE, new Color(220, 50, 50)
                );
                g2d.setPaint(gradient);
                g2d.fillOval(targetX, targetY, TARGET_SIZE, TARGET_SIZE);
                
                // Inner highlight
                g2d.setColor(new Color(255, 200, 200, 100));
                g2d.fillOval(targetX + 10, targetY + 10, TARGET_SIZE - 20, TARGET_SIZE - 20);
                
                // Border
                g2d.setColor(Color.WHITE);
                g2d.setStroke(new BasicStroke(2));
                g2d.drawOval(targetX, targetY, TARGET_SIZE, TARGET_SIZE);
            }
            
            // Draw game over overlay if game is not active
            if (!gameActive) {
                g2d.setColor(new Color(0, 0, 0, 180));
                g2d.fillRect(0, 0, getWidth(), getHeight());
                
                g2d.setFont(new Font("Segoe UI", Font.BOLD, 28));
                g2d.setColor(Color.WHITE);
                String gameOverText = "GAME OVER";
                FontMetrics fm = g2d.getFontMetrics();
                int textX = (getWidth() - fm.stringWidth(gameOverText)) / 2;
                int textY = getHeight() / 2 - 20;
                g2d.drawString(gameOverText, textX, textY);
                
                g2d.setFont(new Font("Segoe UI", Font.PLAIN, 18));
                String scoreText = "Your Score: " + score;
                fm = g2d.getFontMetrics();
                textX = (getWidth() - fm.stringWidth(scoreText)) / 2;
                g2d.drawString(scoreText, textX, textY + 40);
                
                String restartText = "Click 'New Game' to play again";
                fm = g2d.getFontMetrics();
                textX = (getWidth() - fm.stringWidth(restartText)) / 2;
                g2d.drawString(restartText, textX, textY + 80);
            }
        }
    }

    /**
     * Main method - Entry point of the application.
     */
    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> {
            new TargetClickGame().setVisible(true);
        });
    }
}